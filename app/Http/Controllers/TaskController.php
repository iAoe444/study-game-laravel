<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    //TODO 完成其他逻辑的优化
    //TODO 写代码注释
    /*
     *
     * */
    //需要增加openId的验证，成功后返回taskid，判断元素是否存在等等
    public function addTask(Request $request)
    {
        $openId = $request->input('openId');
        $task_content = $request->input('task_content');

        $task = new Task();
        $task->task= $task_content;
        $task->open_id= $openId;
        $task->save();

        return response()->json(['result' => 'success']);
    }

    public function updateTask(Request $request)
    {
        $taskId = $request->input('taskId');
        $task_content = $request->input('task_content');

        $task = Task::find($taskId);
        $task->task = $task_content;
        $bool = $task->save();

        return response()->json(['result' => 'success']);
    }

    /*
     * @parm: openId
     * @return: 包含taskid和task信息的键值对json数据
     * */
    public function getTasks(Request $request)
    {
        $openId = $request->input('openId');
        $tasks = Task::get()
            ->where('open_id','=',$openId)
            ->where('if_complete','=',0);
        $taskArr = array();
        foreach ($tasks as $task) {
            $taskArr[$task->task_id] = $task->task;
        }
        return response()->json(['result' => 'success', 'msg' => $taskArr]);
    }

    public function deleteTask(Request $request)
    {
        $taskId = $request->input('taskId');
        $task = Task::find($taskId);
        $bool = $task->delete();

        return response()->json(['result' => 'success']);
    }

    //TODO 将if_complete的数据库类型换成bool类型
    public function completeTask(Request $request)
    {
        $taskId = $request->input('taskId');

        $task = Task::find($taskId);
        $task->if_complete = 1;
        $bool = $task->save();

        return response()->json(['result' => 'success']);
    }
}
