<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Yaml\Parser;

class UserVoter extends Voter
{
    protected $attributes = array(
        'multipleView',
        'view',
        'edit',
        'resetPassword',
    );

    /**
     * Checks if action(view,edit,etc) supported for entity.
     * @param string $attribute
     * @param mixed $issuedUser
     * @return bool
     */
    protected function supports($attribute, $issuedUser)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, $this->attributes)) {
            return false;
        }
        // in case of list of users
        if (strpos($attribute, 'multiple') !== false) {
            if (!$issuedUser[0] instanceof User) {
                return false;
            }
        } else {
            if (!$issuedUser instanceof User) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if current user can do this action.
     * @param string $attribute
     * @param mixed $issuedUser
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $issuedUser, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        $checkPermissions = 'can' . ucfirst($attribute);
        return $this->$checkPermissions($issuedUser, $user);

        /*switch ($attribute) {
            case self::VIEW:
                return $this->canView($issuedUser, $user);
            case self::EDIT:
                return $this->canEdit($issuedUser, $user);
        }*/

        throw new \LogicException('This code should not be reached!');
    }

    private function canMultipleView(Array $issuedUser, User $user)
    {
        return true;
    }

    private function canView(User $issuedUser, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($issuedUser, $user)) {
            return true;
        }

        // the Post object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return !$issuedUser->isPrivate();
    }

    private function canEdit(User $issuedUser, User $user)
    {
        if ($user->getId() === $issuedUser->getId()) {
            return true;
        }

        $yaml = new Parser();
        $arrayOfOptions = $yaml->parse(file_get_contents(__DIR__ . '/../../config/packages/security.yaml'));
        $hierarchy = $arrayOfOptions['security']['role_hierarchy'];
        $userRoles = $user->getRoles();
        $issuedUserRoles = $issuedUser->getRoles();

        $allSubRoles = $this->getAllSubRoles($hierarchy, $userRoles);
        if (!empty(array_intersect($allSubRoles,$issuedUserRoles))) {
            return true;
        }


        return false;
    }

    public function canResetPassword(User $issuedUser, User $user)
    {
        if ($user->getId() === $issuedUser->getId()) {
            return true;
        }
    }

    protected $allSubRoles = array();

    protected function getAllSubRoles($hierarchy, $userRoles)
    {
        $hierarchyTop = array_keys($hierarchy);
        $hasSubRoles = array_intersect($userRoles, $hierarchyTop);
        foreach ($hasSubRoles as $subRole) {
            if (isset($hierarchy[$subRole])) {
                $this->allSubRoles = array_merge($hierarchy[$subRole], $this->allSubRoles);
                unset($hierarchy[$subRole]);
                unset($hierarchyTop[$subRole]);

            }
        }
        if (empty($hasSubRoles)) {
            $this->allSubRoles = array_unique($this->allSubRoles);
            return $this->allSubRoles;
        }
        return $this->getAllSubRoles($hierarchy, $this->allSubRoles);
    }
}