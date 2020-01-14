<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Record;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OvernightController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "开始留宿";
        return view('overnight.index', ['title' => $title]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|min:2|max:5',
            'hostel' => 'required|size:3',
            'time' => 'required'
        ]);
        session()->flash('username', $request->get('username'));
        session()->flash('hostel', $request->get('hostel'));

        $userData = User::where('user_name', $request->get('username'))->where('user_dormitory', 'like', "%" . $request->get('hostel'))->first();
        if (empty($userData)) return back()->with(['danger' => ' 请输入正确信息！']);

        DB::beginTransaction();
        $userData->staying_status = '是';
        $userData->staying_time = implode(',', $request->get('time'));
        $userData->remarks = $request->get('remarks');

        $setting = Settings::first();
        $week = $setting ? $setting->week ? $setting->week : 1 : 1;

        // 获取用户IP地址
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query=' . $request->getClientIp() . '&resource_id=6006&oe=utf8&format=array&tn=baidu');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // 数据写入到数据库
        $flightRecord = new Record();
        $flightRecord->username = $request->get('username');
        $flightRecord->hostel = $request->get('hostel');
        $flightRecord->week = $week;
        $flightRecord->time = implode(',', $request->get('time'));
        $flightRecord->remarks = $request->get('remarks');
        $flightRecord->user_ip = $request->getClientIp();
        $flightRecord->location = empty(json_decode(curl_exec($ch), true)['data']) ? '获取不到用户地址' : json_decode(curl_exec($ch), true)['data'][0]['location'];
        $flightRecord->user_agent = $request->header()["user-agent"][0];

        curl_close($ch);

        if ($flightRecord->save() && $userData->save()) {
            DB::commit();
            return redirect(route('overnight.index'))->with(['success' => ' 如需修改申请，请重新提交！']);
        } else {
            DB::rollBack();
            return back()->with(['danger' => ' 提交失败，请重试！']);
        }
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $title = "取消留宿";
        return view("overnight.show")->with(['title' => $title]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|min:2|max:5',
            'hostel' => 'required|size:3',
            'time' => 'required'
        ]);

        session()->flash('username', $request->get('username'));
        session()->flash('hostel', $request->get('hostel'));

        $userData = User::where('user_name', $request->get('username'))->where('user_dormitory', 'like', "%" . $request->get('hostel'))->where('staying_time', implode(',', $request->get('time')))->first();
        if (empty($userData)) return back()->with(['danger' => ' 请输入正确信息！']);
        // 判断用户留宿状态
        DB::beginTransaction();
        $userData->staying_status = null;
        $userData->staying_time = null;
        $userData->remarks = null;
        // 判断是否更新成功
        if ($userData->save()) {
            DB::commit();
            return redirect(route('overnight.show'))->with(['success' => ' 如果需要再次提交申请，请到申请页面提交！']);
        } else {
            DB::rollBack();
            return back()->with(['danger' => ' 提交失败，请重试！']);
        }
    }

    /**
     * 导出数据
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function export(Request $request)
    {
        $title = "导出数据";
        if ($request->method("POST") === "POST") {
            $this->validate($request, [
                'password' => 'required|min:6|max:16'
            ]);

            $flight = Settings::first();
            if ($flight) {
                if ($flight->password) {
                    if (!\Hash::check($request->get('password'), $flight->password)) {
                        return back()->with(['danger' => ' 密码错误，请重试！']);
                    }
                    // 判断星期
                    if (date('w', time()) !== 5) return back()->with(['danger' => ' 今天还不是星期五，请耐心等待!']);
                    // 输出数据
                    $week = $flight ? $flight->week ? $flight->week : 1 : 1;
                    $new_files = $flight->class . '第' . $week . '周周末留宿学生名单.xlsx';

                    if (empty($flight->class)) return back()->with(['danger' => ' 请设置班级!']);
                    if (empty($flight->school)) return back()->with(['danger' => ' 请设置学校！']);

                    $datas = User::where('staying_status', '是')->get();
                    $dataNew = [['', '', '', '', $flight->school . '周末留宿学生名单（第' . $week . '周）', '', '', '', ''], ['序号', '班级', '楼层宿舍号', '姓名', '性别', '联系电话', '是否留宿', '留宿时间', '备注']];
                    $dataOld = $datas->toArray();

                    foreach ($dataOld as $key => $data) {
                        $key++;
                        $data['id'] = $key;
                        $dataVal = [];
                        foreach ($data as $val) {
                            array_push($dataVal, $val);
                        }
                        array_push($dataNew, $dataVal);
                    }

                    $export = new UsersExport($dataNew);
                    foreach ($datas as $recovery) {
                        DB::beginTransaction();
                        $recovery->staying_status = null;
                        $recovery->staying_time = null;
                        $recovery->remarks = null;
                        // 判断是否更新成功
                        if ($recovery->save()) {
                            DB::commit();
                        } else {
                            DB::rollBack();
                            return back()->with(['danger' => ' 提交失败，请重试！']);
                        }
                    }
                    DB::beginTransaction();
                    $flight->week = $week + 1;
                    if ($flight->save()) {
                        DB::commit();
                    } else {
                        DB::rollBack();
                    }
                    return Excel::download($export, $new_files);
                } else {
                    $flight->password = bcrypt($request->get('password'));
                    return $flight->save() ? redirect(route('overnight.export'))->with(['success' => '密码创建成功！']) : back()->with(['danger' => '密码创建失败，请重试！']);
                }
            } else {
                $flights = new Settings();
                $flights->password = bcrypt($request->get('password'));
                return $flights->save() ? redirect(route('overnight.export'))->with(['success' => '密码创建成功！']) : back()->with(['danger' => '密码创建失败，请重试！']);
            }
        }
        return view('overnight.export')->with(['title' => $title]);
    }


    public function import(Request $request)
    {
        $title = "导入用户";
        if ($request->method("POST") === "POST") {
            $this->validate($request, [
                'password' => 'required|min:6|max:16'
            ]);

            $flight = Settings::first();
            if ($flight) {
                if ($flight->password) {
                    if (!\Hash::check($request->get('password'), $flight->password)) {
                        return back()->with(['danger' => '密码错误，请重试！']);
                    }

                    $file = $request->file('file');

                    if (!$file->isFile() && $file->isValid() && $file->getError() === 0) {
                        return back()->with(['danger' => '文件上传错误，请重试！']);
                    }

                    try {
                        if (Excel::import(new UsersImport, $file)) {
                            return redirect(route('overnight.import'))->with(['success' => '数据导入成功！']);
                        } else {
                            return back()->with(['danger' => '数据导入失败，请重试！']);
                        }
                    } catch (\PDOException $e) {
                        if ($e->getCode() == '42S02') {
                            return back()->with(['danger' => '数据表不存在，请联系管理员！']);
                        }
                    } catch (\Exception $e) {
                        if ($e->getCode() == 200) {
                            return back()->with(['success' => $e->getMessage()]);
                        }
                        return back()->with(['danger' => '导入数据出现异常，请联系管理员！']);
                    }

                } else {
                    $flight->password = bcrypt($request->get('password'));
                    return $flight->save() ? redirect(route('overnight.import'))->with(['success' => '密码创建成功！']) : back()->with(['danger' => '密码创建失败，请重试！']);
                }
            } else {
                $flights = new Settings();
                $flights->password = bcrypt($request->get('password'));
                return $flights->save() ? redirect(route('overnight.import'))->with(['success' => '密码创建成功！']) : back()->with(['danger' => '密码创建失败，请重试！']);
            }
        }
        return view('overnight.import')->with(['title' => $title]);
    }
}
