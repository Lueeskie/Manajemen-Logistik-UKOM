<?php
/**
 * Controller dasar
 * Bantu load view dan model biar simpel dan rapi.
 */
class Controller
{
    /**
     * Tampilkan view dari folder app/views
     *  $view Nama file view tanpa .php
     *  $data Data opsional yang bisa dipakai di view
     */
    
    public function view($view, $data = [])
    {
        
        require_once __DIR__ . '/../views/' . $view . '.php';
    }

    
    public function model($model)
    {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model;
    }
}
