<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    

class TasksController extends Controller
{
   public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
        
        // ログインしていたら
        $data = [];
        if (\Auth::check()) { 
            $user = \Auth::user();
            // ログインユーザーのタスク一覧を取得して
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            
            $data = [
                    'user' => $user,
                    'tasks' => $tasks,
                ];
            // tasks.indexを返す
            return view('tasks.index', $data);
      
        // ログインしていなかったら
        } else {
        // サインアップとログインへのリンクのみのwelcomeを返す
            return view('welcome');
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;

        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        
        $request->validate([
            'status' => 'required|max:10',  
            'content' => 'required|max:255',
        ]);
        
        // $request->user()->tasks()->create([
        //     'status' => $request->status,
        //     'content' => $request->content,
        // ]);
        
        $task = new Task;
        $task->user_id = \Auth::id();
        $task->status  = $request->status; 
        $task->content = $request->content;
        
        $task->save();
        
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);

        // もし、自分のタスクじゃなかったら、トップにリダイレクトする。
        if (\Auth::id() !== $task->user_id) {
            return redirect('/');
        }
        
        return view('tasks.show',['task'=>$task,]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);

        if (\Auth::id() !== $task->user_id) {
            return redirect('/');
        }
        
        return  view('tasks.edit',['task'=>$task,]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|max:10',  
            'content' => 'required|max:255',  
        ]);
        $task = Task::findOrFail($id);
        if (\Auth::id() !== $task->user_id) {
            return redirect('/');
        }
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();

        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function destroy($id)
    {
        $task = Task::findOrFail($id);
       if (\Auth::id() === $task->user_id) {
        $task->delete();
        }
        return back();
    }
}
