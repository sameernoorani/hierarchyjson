<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Employees extends Controller
{

    /**
     * [employeesList Creates a unique list of employees, as an array.]
     * @param  [array] $employees raw json data
     * @return [array] $employee_array  processed list with unique names of employees.
     */

    public function employeesList($employees){

      /* $employee array will consist of {$id => $name, $id => name, etc. }*/

      $employee_array = array();

      /* Go through the json data, assemble unique list of employee names.
      Then use the generated list of employee names to create an organized */

      foreach ($employees as $employee => $manager) {
          if (!(in_array($employee, $employee_array))) {
              $employee_array[] = $employee;
          }

          if (!(in_array($manager, $employee_array))) {
              $employee_array[] = $manager;
          }
      }

      return $employee_array;

    }

    public function buildTree($employees)
    {

        $employee_array = self::employeesList($employees);

        /* $employee_data will consist of an array of arrays.  Each subarray will contain the data of employee such as
        their manager id, their id, their name.  The id will be generated using $employee_array */

        $employees_data = [];

        foreach ($employees as $employee => $manager) {
            $e_id = array_search($employee, $employee_array);
            $m_id = array_search($manager, $employee_array);

            $employees_data[$e_id] = array("id" => "$e_id", "name" => $employee, "manager_id" => "$m_id", "manager_name" => $manager);
        }


        /* find out which employees are not managers */

        $managers = [];

        foreach ($employees_data as $employee) {
            $managers[$employee['manager_id']] = $employee['manager_id'];
        }

        $not_managers = array_diff_key($employee_array, $managers);


        /* add bosses to the $employees_data */

        $boss = array_diff_key($employee_array, $employees_data);

        foreach ($boss as $id => $name) {
            $employees_data[$id] = array("id" => "$id", "name" => $name, "manager_id" => "", "manager_name" => "");
        }

        /* convert from array of arrays to array of objects */

        $employees_data = array_map(function ($node) {
            return (object) $node;
        }, $employees_data);


        /* iterate through the object datatype and organize the employees */

        $organized = array_map(function ($node) use ($employees_data, $not_managers) {
            if (!empty($node->manager_id)) {
                /* an employee with a manager, place under right employee, return null */

                $name = $node->manager_name;
                $mid = $node->manager_id;

                /* unset un-needed values for the json */

                unset($node->manager_id);
                unset($node->manager_name);
                if (array_key_exists($node->id, $not_managers)) {
                    $namein = $node->name;
                    $node->$namein = [];
                }
                unset($node->id);
                unset($node->name);

                $employees_data[$mid]->$name[] = $node;


                return null;
            } else {
                /* an employee without a manager, a boss -- remove un-needed data */
                unset($node->name);
                unset($node->manager_name);
                unset($node->id);
                unset($node->manager_id);
            }


            return $node;
        }, $employees_data);

        /* clear null entries and extract values to remove index data */

        $organized= array_values(array_filter($organized));


        return json_encode($organized, JSON_PRETTY_PRINT);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        /* retrieve data, pass it to tree function */
        $data = $request->json()->all();

        return self::buildTree($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
