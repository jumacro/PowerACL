<?php

namespace Sdfcloud\Acl;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Sdfcloud\Acl\Models\Role;

/**
 * PermissionsController
 * 
 * This controller will manage the resources of user roles
 * 
 * PHP 5.0 / Laravel 4.0
 * 
 * @author        Mithun Das (mithundas79) on behalf of Pinpoint Media Design (pinpointgraphics)
 * @copyright     Copyright 2014, Pinpoint Media Design
 * @package       app.Controller
 * @property      User $User
 * @since         SDFCloud 3.0
 * 
 */
class RolesController extends BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $user = \Illuminate\Support\Facades\Auth::user();
        $userRole = Models\UserRole::where('user_id', '=', $user->id)->first();
        if ($userRole)
            $role = Role::find($userRole->role_id);
        if ($user->is_admin == 1) {
            return Response::json(Role::get());
        } elseif (!empty($role) && $role->name == 'Super User') {
            return Response::json(Role::get());
        } else {
            return Response::json(Role::where('name', 'NOT LIKE', 'Super%')->get());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        $rules = array(
            'name' => 'required|unique:roles'
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            $role = new Role;
            $role->name = Input::get('name');

            $role->save();

            $insertedId = $role->id;

            return Response::json(array('success' => true, 'message' => 'Role has been created successfully.', 'data' => array('Role' => $role->toArray())));
        } else {
            // validation has failed, display error messages  
            $messages = $validator->messages();
            $errorData = array();
            foreach ($messages->all() as $message) {
                $errorData[] = $message;
            }
            $resposeData = array(
                'success' => false,
                'message' => "Failed to add role",
                'data' => array('errors' => $errorData)
            );

            return Response::json($resposeData);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $role = Role::find($id)->toArray();


        return Response::json(array('success' => true, 'roleData' => $role));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $rules = array(
            'name' => 'required|unique:roles'
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            $role = Role::find($id);
            $role->name = Input::get('name');
            $role->save();

            return Response::json(array('success' => true, 'message' => 'Role has been saved successfully.', 'data' => array('Role' => $role->toArray())));
        } else {
            // validation has failed, display error messages  
            $messages = $validator->messages();
            $errorData = array();
            foreach ($messages->all() as $message) {
                $errorData[] = $message;
            }
            $resposeData = array(
                'success' => false,
                'message' => "Failed to save role",
                'data' => array('errors' => $errorData)
            );

            return Response::json($resposeData);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        Role::destroy($id);

        return Response::json(array('success' => true));
    }

}
