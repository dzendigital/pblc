<?php
namespace App\Interfaces;


interface ServeyInterface
{
    public function all();
    public function save($validated);
    public function storagelink($id);
}

?>