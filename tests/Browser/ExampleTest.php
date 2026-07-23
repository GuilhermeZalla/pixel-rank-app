<?php

it('registers a user', function(){
   visit('/register')
   ->fill('name', 'John Doe')
   ->fill('email', 'zallaguilherme@gmail.com')
   ->fill('password', 'password1234')
   ->click('Create Account')
   ->assertPath('/');
});

$this->assertAuthenticated();

