<?php
class Memo extends Eloquent{
    //This is a white list to allow you
    //to insert values into multiple column at the same time
    protected $fillable = array(
        'user_id',
        'title',
        'memo',
        'urlpath',
        'status',
        'created_at',
        'updated_at'
        );
}