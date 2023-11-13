<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LdapRecord\Container;

class TesteController extends Controller
{
    public function teste(Request $request) {
        $user = Container::getConnection('default')->query()->where('samaccountname',$request->username)->get();
        // $user = Container::getConnection('default')->query()->where('objectguid',$this->str_to_guid($request->username))->get();
        print_r($user);
    }

    public function teste2(Request $request) {
        
        $file = fopen($request->file, 'r');
        $content = file_get_contents('caminho/do/arquivo.txt');
        fclose($file);

        $lines = explode("\n", $content);

        $start = false;
        $end = false;
        $content = '';

        foreach ($lines as $line) {
            if (strpos($line, 'Objetivo') !== false) {
                $start = true;
            } elseif (strpos($line, "3") !== false) {
                $end = true;
                break;
            } elseif ($start) {
                $content .= $line . "\n";
            }
        }

        return $content;
    }

    private function guid_to_str($binary_guid){
        $unpacked = unpack('Va/v2b/n2c/Nd', $binary_guid);
        $uuid = sprintf('%08X-%04X-%04X-%04X-%04X%08X', $unpacked['a'], $unpacked['b1'], $unpacked['b2'], $unpacked['c1'], $unpacked['c2'], $unpacked['d']);
        return mb_strtolower($uuid);
    }

    private function str_to_guid(string $uuidString): string{
        $uuidString = str_replace('-', '', $uuidString);
        $pieces = [
            ltrim(substr($uuidString, 0, 8), '0'),
            ltrim(substr($uuidString, 8, 4), '0'),
            ltrim(substr($uuidString, 12, 4), '0'),
            ltrim(substr($uuidString, 16, 4), '0'),
            ltrim(substr($uuidString, 20, 4), '0'),
            ltrim(substr($uuidString, 24, 4), '0'),
            ltrim(substr($uuidString, 28, 4), '0'),
        ];
        $pieces = array_map('hexdec', $pieces);
        return pack('Vv2n4', ...$pieces);
    }
}
