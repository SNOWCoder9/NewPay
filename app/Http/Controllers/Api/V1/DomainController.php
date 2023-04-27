<?php

namespace App\Http\Controllers\Api\V1;

use App\Enum\DomainEnum;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DomainController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request)
    {
        $pageSize = (int)$request->get('pageSize', 10);
        $user = $request->user();
        $data = Domain::query()->where('user_id', $user->id)->orderByDesc('id')->paginate($pageSize);

        return response()->json(['code' => 1, 'data' => $data]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $id = (int)$request->post('id');
        Domain::query()->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->delete();

        return response()->json(['code' => 1, 'message' => "删除成功"]);
    }

    public function submit(Request $request)
    {
        $domain = $request->post('domain');
        if (! $domain){
            return response()->json(['code' => 2, 'message' => "请输入域名"]);
        }
        if (getConfig('pay_domain_open', 'true') == 'true'){
            $status = DomainEnum::REVIEW;
        } else {
            $status = DomainEnum::SUCCESS;
        }
        $model = new Domain();
        $model->user_id = $request->user()->id;
        $model->domain = $domain;
        $model->status = $status;
        $model->save();
        if ($status === DomainEnum::SUCCESS){
            $domain_list = Domain::query()->where('status', DomainEnum::SUCCESS)->pluck('domain')->toArray();
            Cache::forever('domain_white_list', $domain_list);
        }

        return response()->json(['code' => 1, 'message' => "提交成功，请耐心等待审核!"]);
    }
}
