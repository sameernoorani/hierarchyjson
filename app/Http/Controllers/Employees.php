<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;

class Employees extends Controller
{

    /**
     * [employeesList Creates a unique list of employees, as an array.]
     * @param  [array] $employees raw json data
     * @return [array] $employee_array  processed list with unique names of employees.
     */

    public function employeesList($employees)
    {
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

    /**
     * [employeesData Return employee data as array of arrays]
     * @param  [type] $employee_array processed list with unique names of employees.
     * @param  [type] $employees      raw employees data.
     * @return [type]                 $employee_data consists of an array of arrays, of employee data.
     *                                format: $array[$employee_id] => array("id" => "$e_id", "name" => $employee, "manager_id" => "$m_id", "manager_name" => $manager)
     */
    public function employeesData($employee_array, $employees)
    {
        $employees_data = [];

        foreach ($employees as $employee => $manager) {
            $e_id = array_search($employee, $employee_array);
            $m_id = array_search($manager, $employee_array);

            $employees_data[$e_id] = array("id" => "$e_id", "name" => $employee, "manager_id" => "$m_id", "manager_name" => $manager);
        }

        return $employees_data;
    }

    /**
     * [notManagers Returns array of non-manager employees.]
     * @param  [type] $employees_data $employee_data consists of an array of arrays, of employee data.
     * @param  [type] $employees      raw employees data.
     * @param  [type] $employee_array processed list with unique names of employees.
     * @return [type]                 return list of employees that aren't managers.
     */
    public function notManagers($employees_data, $employees, $employee_array)
    {
        $managers = [];

        foreach ($employees_data as $employee) {
            $managers[$employee['manager_id']] = $employee['manager_id'];
        }

        $not_managers = array_diff_key($employee_array, $managers);

        return $not_managers;
    }

    /**
     * [buildTree given the json data, build a tree]
     * @param  [type] $employees employees data converted to array
     * @param  [type] $count     number of lines of data provided in the initial json string.
     * @return [type]            organized employee data.
     */
    public function buildTree($employees, $count, $graph)
    {

        /* $employee array will consist of {$id => $name, $id => name, etc. }*/

        $employee_array = self::employeesList($employees);

        /* $employee_data will consist of an array of arrays.  Each subarray will contain the data of employee such as
        their manager id, their id, their name.  The id will be generated using $employee_array */

        $employees_data = self::employeesData($employee_array, $employees);

        /* find out which employees are not managers */

        $not_managers = self::notManagers($employees_data, $employees, $employee_array);


        $boss = array_diff_key($employee_array, $employees_data);

        /* add bosses to the $employees_data */

        foreach ($boss as $id => $name) {
            $employees_data[$id] = array("id" => "$id", "name" => $name, "manager_id" => "", "manager_name" => "");
        }

        /* check for errors in data */

        if (count($boss) == 0) {
            /* no head boss, invalid data */
            return "There is a loop or invalid data";
        }

        if (count($not_managers) == 0) {
            /* no endpoints, invalid data */
            return "There is a loop or invalid data";
        }

        if ($count != count($employees) && $count != -1) {
            /* looks like there was a repeated/duplicate key data */
            return "There is a loop or invalid data";
        }


        /* convert from array of arrays to array of objects */

        $employees_data = array_map(function ($node) {
            return (object) $node;
        }, $employees_data);


        /* iterate through the object datatype and organize the employees */

        $organized = array_map(function ($node) use ($employees_data, $not_managers, $graph) {
            if (!empty($node->manager_id)) {
                /* an employee with a manager, place under right employee, return null */

                $name = $node->manager_name;
                $mid = $node->manager_id;

                /* unset un-needed values for the json */

                unset($node->manager_id);
                unset($node->manager_name);
                if (array_key_exists($node->id, $not_managers) && $graph == 0) {
                    $namein = $node->name;
                    $node->$namein = [];
                }

                if ($graph == 0) {
                  unset($node->id);
                  unset($node->name);
                  $employees_data[$mid]->$name[] = $node;
                } else {
                  $employees_data[$mid]->children[] = $node;
                }

                return null;
            } else {
                /* an employee without a manager, a boss -- remove un-needed data */
                if ($graph == 0){
                  unset($node->name);
                  unset($node->id);
                }
                unset($node->manager_name);
                unset($node->manager_id);
            }

            return $node;
        }, $employees_data);

        /* clear null entries and extract values to remove index data */

        $organized = array_values(array_filter($organized));

        return json_encode($organized[0], JSON_PRETTY_PRINT);
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

        $raw_data = $request->getContent();

        $count = substr_count($raw_data, ':');

        $data = $request->json()->all();

        return self::buildTree($data, $count, $graph = 0);
    }

    /**
     * Display graph of the submitted json
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function graph(Request $request)
    {
        /* retrieve data, pass it to tree function */

        $raw_data = $request->getContent();

        $count = substr_count($raw_data, ':');

        $data = $request->json()->all();

        return self::buildTree($data, $count, $graph = 1);

    }

    /**
     * [upload description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */

    public function upload(Request $request){

      /* store the uploaded file, convert to json data, pass it to parser function */
      $json = $request->file('json');

      $input['jsonname'] = time().'.'.$json->getClientOriginalExtension();

      $destinationPath = public_path('/json');

      $json->move($destinationPath, $input['jsonname']);

      $string = File::get($destinationPath.'/'.$input['jsonname']);

      $count = substr_count($string, ':');

      $json_file = json_decode($string, true);

      $data = self::buildTree($json_file, $count, $graph = 1);


      if ($data == 'There is a loop or invalid data'){
        $data = [];
        $data['error'] = 'There is a loop or invalid data';
      }

      return view('index')->with('data', $data);


    }


}
