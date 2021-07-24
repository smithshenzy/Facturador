<?php
namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\System\Configuration;



class ConfigurationController extends Controller
{

    public function index()
    {
        $configuration = Configuration::first();
        return view('system.configuration.index', compact('configuration'));
    }

    public function record()
    {

        $configuration = Configuration::first();

        return [
            'token_public_culqui' => $configuration->token_public_culqui,
            'token_private_culqui' => $configuration->token_private_culqui,
        ];
    }


    public function store(Request $request)
    {
        $configuration = Configuration::first();

        if($request->token_public_culqui)
        {
            $configuration->token_public_culqui = $request->token_public_culqui;
        }

        if($request->token_private_culqui)
        {
            $configuration->token_private_culqui = $request->token_private_culqui;
        }

        if($request->url_apiruc)
        {
            $configuration->url_apiruc = $request->url_apiruc;
        }

        if($request->token_apiruc)
        {
            $configuration->token_apiruc = $request->token_apiruc;
        }

        $configuration->save();

        return [
            'success' => true,
            'message' => 'Datos guardados con exito'
        ];
    }

    public function apiruc()
    {

        $configuration = Configuration::first();

        return [
            'url_apiruc' => $configuration->url_apiruc,
            'token_apiruc' => $configuration->token_apiruc,
        ];
    }

    public function storeLoginSettings()
    {
        request()->validate([
            'position_form' => 'required|in:left,right',
            'show_logo_in_form' => 'required|boolean',
            'position_logo' => 'required|in:top-left,top-right,bottom-left,bottom-right,none',
            'show_socials' => 'required|boolean',
            'use_login_global' => 'required|boolean',
        ]);

        $config = Configuration::first();
        $loginConfig = $config->login;
        foreach(request()->all() as $key => $option) {
            $loginConfig->$key = $option;
        }

        $config->login = $loginConfig;
        $config->use_login_global = request('use_login_global');
        $config->save();

        return response()->json([
            'success' => true,
            'message' => 'Información actualizada.',
        ], 200);
    }

    public function storeBgLogin()
    {
        request()->validate([
            'image' => 'required|mimes:webp,jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $config = Configuration::first();
        if (request()->hasFile('image') && request()->file('image')->isValid()) {
            $file = request()->file('image');
            $ext = $file->getClientOriginalExtension();
            $name = time() . '.' . $ext;
            $path = 'public/uploads/login';
            $file->storeAs($path, $name);

            $loginConfig = $config->login;
            $basePathStorage = 'storage/uploads/login/';
			if (request('type') === 'bg') {
                $loginConfig->type = 'image';
				$loginConfig->image = asset($basePathStorage . $name);
			} else {
                $loginConfig->logo = asset($basePathStorage . $name);
            }
            $config->login = $loginConfig;
            $config->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Información actualizada.',
        ], 200);
    }

    public function InfoIndex(){
        $memory_limit = ini_get('memory_limit');
        $memory_in_byte = number_format(self::return_bytes($memory_limit),'2',',','.');
        $pcre_backtrack_limit = ini_get('pcre.backtrack_limit');

        return view('system.configuration.info', compact(
            'memory_limit',
            'memory_in_byte',
            'pcre_backtrack_limit'
        ));

    }

    private  static function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = substr($val, 0, -1);
        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }
}