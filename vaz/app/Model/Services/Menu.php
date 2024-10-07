<?php
declare(strict_types=1);

namespace App\Model\Services;

class Menu
{
    public function getMenu(): array
    {
        return [
            ['logged' => false, 'role' => '','title' => 'Úvod - Home page', 'name' => 'Úvod', 'link' => 'Home:default', 'alwaysAvailable' => true],
            ['logged' => false, 'role' => '','title' => 'O nás - About', 'name' => 'O Nás', 'link' => 'Page:onas', 'alwaysAvailable' => true],
            ['logged' => false, 'role' => '','title' => 'Seznam azylů - list of azyls', 'name' => 'Azyly', 'link' => 'Home:azyls', 'alwaysAvailable' => true],
            ['logged' => false, 'role' => '','title' => 'Seznam zvířat k adopci - List of animals for adoption', 'name' => 'Zvířátka k adopci', 'link' => 'Home:adoptions', 'alwaysAvailable' => true],
            ['logged' => true, 'role' => 'admin','title' => 'Administrace', 'name' => 'Administrace', 'link' => 'Admin:default', 'alwaysAvailable' => false],
            ['logged' => true, 'role' => 'azyl','title' => 'Můj azyl - Azyl administration', 'name' => 'Můj azyl', 'link' => 'Azyl:default', 'alwaysAvailable' => false],
            ['logged' => true, 'role' => '','title' => 'Profil uživatele', 'name' => 'Profil', 'link' => 'User:default', 'alwaysAvailable' => false],
            ['logged' => true, 'role' => 'superadmin','title' => 'Pretorian administration', 'name' => 'Administrace', 'link' => 'Admin:default', 'alwaysAvailable' => false],
            ['logged' => true, 'role' => 'superadmin','title' => 'Pretorian azyl admin', 'name' => 'Můj azyů', 'link' => 'Azyl:default', 'alwaysAvailable' => false],
            ['logged' => true, 'role' => 'superadmin','title' => 'Pretorian home page', 'name' => 'π', 'link' => 'SuperAdmin:default', 'alwaysAvailable' => false]
        ];
    }

    public function getAdminMenu(): array
    {
        return [
            ['logged' => true, 'role' => 'admin', 'name' => 'Správa uživatelů', 'link' => 'Admin:users'],
            ['logged' => true, 'role' => 'admin', 'name' => 'Správa zvířat', 'link' => 'Admin:animals'],
            ['logged' => true, 'role' => 'admin', 'name' => 'Správa adopcí', 'link' => 'Admin:adoptions'],
            ['logged' => true, 'role' => 'admin', 'name' => 'Správa azylů', 'link' => 'Admin:azyls'],
            ['logged' => true, 'role' => 'admin', 'name' => 'Správa zájemců', 'link' => 'Admin:owners'],
            ['logged' => true, 'role' => 'admin', 'name' => 'Správa novinek', 'link' => 'Admin:news'],
            ['logged' => true, 'role' => 'admin', 'name' => 'Správa sponzorů', 'link' => 'Admin:sponsors'],
            ['logged' => true, 'role' => 'admin', 'name' => 'Správa příspěvků', 'link' => 'Admin:news'],
            ['logged' => true, 'role' => 'admin', 'name' => 'Správa komentářů', 'link' => 'Admin:page']
        ];
    }

    public function getSuperAdminMenu():array
    {
        return [
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa uživatelů', 'link' => 'SuperAdmin:users'],
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa zvířat', 'link' => 'SuperAdmin:animals'],
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa adopcí', 'link' => 'SuperAdmin:adoptions'],
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa azylů', 'link' => 'SuperAdmin:azyls'],
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa zájemců', 'link' => 'SuperAdmin:owners'],
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa fotek', 'link' => 'SuperAdmin:photos'],
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa rolí', 'link' => 'SuperAdmin:roles'],
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa vzkazů', 'link' => 'SuperAdmin:vzkazů'],
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa novinek', 'link' => 'SuperAdmin:news'],
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa sponzorů', 'link' => 'SuperAdmin:sponsors'],
            ['logged' => true, 'role' => 'superadmin', 'name' => 'Správa komentářů', 'link' => 'SuperAdmin:page']
        ];
    }

    public function getAzylMenu():array
    {
        return [
            ['logged' => true, 'role' => 'azyl', 'name' => 'Správa zvířat', 'link' => 'Azyl:animals'],
            ['logged' => true, 'role' => 'azyl', 'name' => 'Správa adopcí', 'link' => 'Azyl:adoptions'],
            ['logged' => true, 'role' => 'azyl', 'name' => 'Správa zájemců', 'link' => 'Azyl:owners'],
            ['logged' => true, 'role' => 'azyl', 'name' => 'Správa fotek', 'link' => 'Azyl:photos'],
            ['logged' => true, 'role' => 'azyl', 'name' => 'Správa novinek', 'link' => 'Azyl:news']
        ];
    }

    public function getUserMenu():array
    {
        return [
            ['logged' => true, 'role' => 'user', 'name' => 'Profil', 'link' => 'User:default'],
            ['logged' => true, 'role' => 'user', 'name' => 'Moje adopce', 'link' => 'User:adoptions'],
            ['logged' => true, 'role' => 'user', 'name' => 'Moje zvířátka', 'link' => 'User:animals'],
            ['logged' => true, 'role' => 'user', 'name' => 'Moje fotky', 'link' => 'User:photos']
        ];
    }

    public function getMainMenu():array
    {
        return [
            ['logged' => false, 'role' => '', 'name' => 'Podpora', 'link' => 'Home:support', 'alwaysAvailable' => true],
            ['logged' => false, 'role' => '', 'name' => 'Č.K.D', 'link' => 'Home:faq', 'alwaysAvailable' => true],
            ['logged' => false, 'role' => '', 'name' => 'Podpořili nás', 'link' => 'Home:support', 'alwaysAvailable' => true],
            ['logged' => false, 'role' => '', 'name' => 'Přihlášení', 'link' => 'Home:signIn'],
            ['logged' => false, 'role' => '', 'name' => 'Registrace', 'link' => 'Home:register'],
            ['logged' => true, 'role' => '', 'name' => 'Odhlásit', 'link' => 'Home:logOut']
        ];
    }
}