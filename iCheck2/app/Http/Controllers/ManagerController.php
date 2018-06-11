<?php

namespace App\Http\Controllers;

use App\CheckInLog;
use App\Event;
use App\Http\Requests\EventPost;
use App\JoinEvent;
use App\Member;
use App\Namelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(function($request, $next){
            if(Event::where('id', $request->id)->value('owner_id') == $request->user()->id) {
                return $next($request);
            }
            return redirect(route('manager_index'));

        })->only(['check', 'edit', 'update', 'delete']);
    }

    public function index(Request $request)
    {
        $events = Event::where('owner_id', Auth::id())->orderBy('id','desc')->get();

        return view('manager.index', compact('events'));
    }


    public function info(Request $request)
    {
        $event = Event::where('id', $request->id)->first();
        return view('manager.info', compact('event'));
    }

    public function check(Request $request)
    {
        $event = Event::where('id', $request->id)->first();
        $logs = CheckInLog::where('event_id', $request->id)->orderBy('checkin_time', 'desc')->get();
        return view('manager.check', compact('event'), compact('logs'));
    }

    public function edit(Request $request)
    {
        $event = DB::table('events')->select(DB::raw('*, DATE_FORMAT(`hold_time`, \'%Y-%m-%dT%H:%i\') AS custom_time'))->where('id', $request->id)->first();
        $namelists = $this->get_namelist_by_userid($request->user()->id);
        return view('manager.edit', compact(['event', 'namelists']));
    }

    public function update(EventPost $request)
    {
        $v = $this->validate_type_namelist($request->type, $request->namelist_id);

        if($v['validation']) {
            Event::where('id', $request->id)
                ->update([
                    'name' => $request->name,
                    'content' => $request->content ? $request->content : NULL,
                    'type' => $request->type,
                    'hold_time' => $request->hold_time,
                    'member_quantity' => $request->member_quantity,
                    'namelist_id' => $request->namelist_id
                ]);
        } else {
            return redirect( route('manager_edit', ['id', $request->id]))->withErrors($v['emsg'])->withInput();
        }

        return redirect( route('manager_index'));
    }

    public function create(EventPost $request)
    {
        $v = $this->validate_type_namelist($request->type, $request->namelist_id);

        if($v['validation']) {
            Event::insert([
                'owner_id' => $request->user()->id,
                'name' => $request->name,
                'content' => $request->content ? $request->content : NULL,
                'type' => $request->type,
                'hold_time' => $request->hold_time,
                'member_quantity' => $request->member_quantity,
                'namelist_id' => $request->namelist_id
            ]);
        } else {
            return redirect( route('manager_create_view'))->withErrors($v['emsg'])->withInput();
        }

        return redirect( route('manager_index'));

    }

    public function create_show(Request $request)
    {
        $namelists = $this->get_namelist_by_userid($request->user()->id);
        return view('manager.create', compact('namelists'));
    }

    public function delete(Request $request)
    {
        $event = Event::where('id', $request->id);
        if($event->exists()) {
            $event->delete();
            return response()->json(['msg' => 1]);
        }
        return response()->json(['msg' => 0]);
    }

    public function namelist_show(Request $request)
    {

    }

    public function checkAPI(Request $request)
    {
        $result = [];
        $id = $this->validateStudentId($request->_id);
        if($id) {
            $event_type = Event::where('id', $request->event_id)->value('type');
            if($this->check_check_in($id, $request->event_id)) {
                $result['in'] = 1;
            } else {
                switch($event_type) {
                    //0 for don't need registration
                    //1 for need registration
                    //2 for need registration but can registration when check in
                    case 0:
                        CheckInLog::insert([
                            'event_id' => $request->event_id,
                            'member_id' => $id
                        ]);
                        break;

                    case 1:
                        if(JoinEvent::where([['member_id', "=", $id],['event_id', '=', $request->event_id]])->exists()) {
                            CheckInLog::insert([
                                'event_id' => $request->event_id,
                                'member_id' => $id
                            ]);
                            $result['registration'] = 1;
                        } else {
                            $result['registration'] = 0;
                        }
                        break;

    //                    case 2:
    //                        if(JoinEvent::where('member_id', $id)->get())
    //                        {
    //                            CheckInLog::insert([
    //                                'event_id' => $request->event_id,
    //                                'member_id' => $id
    //                            ]);
    //                            $result['registration'] = 1;
    //                        } else {
    //                            $result['registration'] = 0;
    //                        }
                }
                $result['in'] = 0;
            }
            $logs = CheckInLog::where('event_id', $request->event_id)->orderBy('checkin_time', 'desc')->get();
            $result['logs'] = $logs;
            $result['id'] = $id;
            $result['msg'] = 1;
        } else {
            $result['msg'] = 0;
        }

        return response()->json($result);
    }

    private function check_check_in($id, $event_id)
    {

        if(CheckInLog::where([
            ['member_id', '=', $id],
            ['event_id', '=', $event_id]
        ])->exists()) {
            return True;
        } else {
            return False;
        }
    }

    private function validateStudentId($raw_id)
    {
        $id = NULL;
        if(preg_match("/^([A-Za-z][0-9]{8,8})/", $raw_id)) {
            $id = Member::where('std_id', $raw_id);
        }
        elseif(preg_match("/^([0-9]{10,10})/", $raw_id)) {
            $id = Member::where('card_id', $raw_id);
        } else {
            return 0;
        }
        if($id->exists()) {
            return $id->value('std_id');
        } else {
            return 0;
        }
    }

    public function test()
    {
        $i = CheckInLog::where('member_id', 'B10304044');
        if($i->exists()) {
            return "1";
        }
        return "0";
    }

    public function validate_type_namelist($type, $namelist_id)
    {
        if($type!="0" && $namelist_id=="0") {
            return ['validation' => False, 'emsg' => ['namelist_id' => '請選擇名冊']];
        } else if( $type=="0" && $namelist_id!="0") {
            return ['validation' => False, 'emsg' => ['type' => '幹你娘不要亂改']];
        } else {
            return ['validation' => True];
        }
    }

    public function get_namelist_by_userid($user_id)
    {
        return Namelist::where('owner_id', $user_id)->get();
    }
}


