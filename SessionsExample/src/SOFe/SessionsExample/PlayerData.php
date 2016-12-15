<?php

namespace SOFe\SessionsExample;

class PlayerData{
    // These two class properties are not used for changeable variables, but
    // it is best if we keep a reference to these values so that we can know
    // what this object is about just by passing this object around.
    private $main;
    private $username;

    private $joins = 0;
    private $onlineTime = 0;
    private $lastOnline = 0;

    public function __construct(MainClass $main, string $username){ // we need to pass the main class instance too so that PlayerData knows which directory to save in
        // my practice is to always put these lines for defining immutable (cannot be
        // changed) class properties in the beginning of a constructor, because the
        // following method calls may use them.
        $this->main = $main;
        $this->username = $username;

        // I am making the path into a function because I know that
        // we will use it again later, when we save data.
        // So if we change the file path, we don't need to change two places, just one place. This avoids so many strange bugs
        $path = $this->getPath();
        if(!is_file($path)){ // if the file doesn't exist, i.e. player never joined this server after this plugin is installed
            return; // do nothing, because we will leave everything to their default values
        }

        // load the data in the YAML file into memory!
        $data = yaml_parse_file($path); // use this instead of `(new Config(...))->getAll()`!
        $this->joins = $data["joins"];
        $this->onlineTime = $data["onlineTime"];
        $this->lastOnline = $data["lastOnline"];
    }

    public function getPath() : string{
        // Hint: remember to strtolower(), because some filesystems are case-sensitive!
        return $this->main->getDataFolder() . strtolower($this->username) . ".yml";
    }

    public function getJoins() : int{
        return $this->joins;
    }

    public function getOnlineTime() : float{
        return $this->onlineTime;
    }

    public function getLastOnline() : float{
        return $this->lastOnline;
    }
}
