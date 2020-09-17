<?php
namespace App\DAO;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Users;
use App\Level;
use App\UserUpgradeLog;

class UserDAO
{
    /**
     * 查询用户的指定代数的上级(根据parents_path信息)
     *
     * @param App\Users $user 用户模型实例
     * @param integer $qty 要取的上级代数,不传或传null则取全部
     * @return array 返回包含上级id的数组
     */
    public static function getParentsPathDesc($user, $qty = null)
    {
        $path = $user->parents_path;
        if ($path == null || empty($path)) {
            return [];
        }
        $parents = explode(',', $path);
        $parents = array_filter($parents);
        krsort($parents);
        $parents = array_slice($parents, 0, $qty);
        return $parents;
    }

    /**
     * 递归查询上级
     *
     * @param App\Users $user 用户模型实例
     * @return array
     */
    public static function getRealParents($user)
    {
        $found_parent_node = [];
        $parents = self::findParent($user, $found_parent_node);
        return $parents;
    }

    /**
     * 递归查询上级(字符串)
     *
     * @param App\Users $user 用户模型实例
     * @return string 返回逗号间隔的path
     */
    public static function getRealParentsPath($user)
    {
        $parents = self::getRealParents($user);
        if (count($parents) > 0) {
            return implode(',', $parents);
        }
        return '';
    }

    private static function findParent($user, &$found_parent_node)
    {
        $parent_id = $user->parent_id;

        if ($parent_id) {
            //检测节点关系是否有死循环
            if (in_array($parent_id, $found_parent_node)) {
                $context = [
                    'user_id' => $user->id,
                    'parent_id' => $parent_id,
                    'found_parent_node' => $found_parent_node,
                ];
                //记录错误日志
                Log::useDailyFiles(base_path('storage/logs/user/'), 7);
                Log::critical('id:'.$user->id . '的用户,上级关系存在死循环', $context);
                return [];
            }
            array_unshift($found_parent_node, $parent_id);
            $parent = Users::find($parent_id);
            $result = self::findParent($parent, $found_parent_node);
            unset($parent);
            array_push($result, $parent_id);
            return $result;
        } else {
            return [];
        }
    }

    /**
     * 检查用户是否符合对应级别的升级,若符合就升级(不降级)
     *
     * @param App\Users $user 要升级的用户模型实例
     * @param App\Users $from_user 触发者用户模型实例
     * @return void 无返回值
     */
    public static function upgradeCheck($user)
    {
        $before_level = $user->level_id;
        $new_level = 2;
        if ($user->is_disable == 1) {
            $new_level = 1;
        }else if ($user->total_integral >= 1000000){
            $new_level = 5;
        }else if ($user->total_topup >= 10000){
            $new_level = 4;
        }else if ($user->total_topup >= 300){
            $new_level = 3;
        }

        //查询等级对应的id
        $level = Level::where('code', $new_level)->first();
        //不掉级处理
        if ($before_level < $new_level) {
            try {
                DB::transaction(function () use ($user , $level, $before_level, $new_level) {
                    $user_upgrade_log = new UserUpgradeLog();
                    $user_upgrade_log->user_id  = $user->id;
                    $user_upgrade_log->from_user_id = $user->id;
                    $user_upgrade_log->before_level = $before_level;
                    $user_upgrade_log->after_level = $new_level;
                    $user_upgrade_log->memo = '用户等级变更:由[' . self::get_level_name($before_level) . ']升级到['. self::get_level_name($new_level) . ']';
                    $user_upgrade_log->created_time = time();
                    $result = $user_upgrade_log->save();
                    if (!$result) {
                        throw new \Exception('记录用户升级日志失败');
                    }
                    $user->level_id = $level->id;
                    $result = $user->save();
                    if (!$result) {
                        throw new \Exception('变更用户等级失败');
                    }
                });
            } catch (\Exception $e) {
                echo '<pre>';
                echo  '错误:' . $e->getMessage() . PHP_EOL . ',文件:' . $e->getFile() . PHP_EOL . '行号:'. $e->getLine();
                return ;
            }
//            $parent = Users::find($user->parent_id);
//            if ($parent) {
//                self::upgradeCheck($parent, $user);
//            }
        }
    }


    public static function get_level_name ($id = 2) {

        $name = '';
        switch ($id) {
            case 1:
                $name = '限制会员';
                break;
            case 2:
                $name = '临时会员';
                break;
            case 3:
                $name = '正式会员';
                break;
            case 4:
                $name = '五星会员';
                break;
            case 5:
                $name = 'VIP会员';
                break;
            default:
                $name = '临时会员';
        }

        return $name;
    }
}
