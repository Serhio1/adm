<?php

namespace App\Service;

use Symfony\Component\Yaml\Parser;

class Roles
{
    public $allSubRoles = array();

    public function getAll($user)
    {
        $yaml = new Parser();
        $arrayOfOptions = $yaml->parse(file_get_contents(__DIR__ . '/../../config/packages/security.yaml'));
        $hierarchy = $arrayOfOptions['security']['role_hierarchy'];
        $userRoles = $user->getRoles();
        $allSubRoles = $this->getAllSubRoles($hierarchy, $userRoles);

        return $allSubRoles;
    }

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