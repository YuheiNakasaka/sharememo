<?php
use Illuminate\Auth\UserInterface;

class User extends Eloquent implements UserInterface
{

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return null;
    }
}
?>
