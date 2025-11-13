<?php
class UserController extends Controller
{
    public function index()
    {
        echo "UserController@index berhasil dipanggil!";
    }

    public function halo()
    {
        echo "<h2>Halo dari method halo()!</h2>";
    }

    public function detail($id)
    {
        echo "<p>Detail user ID: $id</p>";
    }


    public function akun()
    {
        $data['nama'] = "Zenkaizura";
        $this->view('akun', $data);
    }


    public function modeltest()
    {
        $user = $this->model('User');
        echo $user->getName();
    }
}
