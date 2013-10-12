<?php

class Draft extends Eloquent{

    protected $fillable = array(
        'user_id',
        'draft_id',
        'title',
        'memo',
        'created_at',
        'updated_at'
        );

}