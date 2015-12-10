<?php

namespace Notification;


class Storage
{
    private $types;

    function __construct()
    {
        $this->types = array(
            new Type(5, "AlliWarDeclared", "declared war", "#unset-dev"),
            new Type(6, "AlliWarSurrendered", "surrendered", "#unset-dev"),
            new Type(8, "AlliWarEnding", "war ends within 24 hours", ""),
            new Type(16, "MemberApplication", "applied to join", "#unset-dev"),
            new Type(21, "MemberLeft", "left corp", "#unset-dev"),
            new Type(75, "TowerAlert", "tower under attack", "#unset-dev"),
            new Type(75, "TowerFuel", "tower low on fuel", "#unset-dev"),
            new Type(128, "MemberJoined", "joined corp", "#unset-dev"),
        );
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }
}
