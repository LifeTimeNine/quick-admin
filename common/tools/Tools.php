<?php

namespace tools;

use traits\tools\Instance;

/**
 * 常用工具类
 */
class Tools
{
    use Instance;

    /**
     * 数组转树结构
     * @access  public
     * @param   array       $data       源数据
     * @param   mixed       $pid        父级ID
     * @param   callable    $callable   回调处理
     * @param   string      $pkKey      主键Key
     * @param   string      $parentKey  父级Key
     * @param   int         $tier       层级
     * @return  array
     */
    public function arr2tree($data, $pid = 0, callable $callable = null, string $pkKey = 'id', string $parentKey = 'pid', int $tier = 1)
    {
        $result = [];
        foreach ($data as $item) {
            if ($item[$parentKey] == $pid) {
                $item['children'] = $this->arr2tree($data, $item[$pkKey], $callable, $pkKey, $parentKey, $tier + 1);
                if (is_callable($callable)) {
                    $item = call_user_func_array($callable, [$item, $tier]);
                    if ($item === false) continue;
                }
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * 删除文件夹及其所有子文件
     * @access  public
     * @param   string   $path   文件目录
     */
    public function delDir($path)
    {
        //如果是目录则继续
        if (is_dir($path)) {
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach ($p as $val) {
                //排除目录中的.和..
                if ($val != "." && $val != "..") {
                    //如果是目录则递归子目录，继续操作
                    if (is_dir($path . '/' . $val)) {
                        //子目录中操作删除文件夹和文件
                        $this->delDir($path . '/' . $val);
                        //目录清空后删除空文件夹
                        @rmdir($path . '/' . $val);
                    } else {
                        //如果是文件直接删除
                        unlink($path . '/' . $val);
                    }
                }
            }
            @rmdir($path);
        }
    }
    /**
     * 根据出生年月日获取年龄
     * @param  string   $birthday   出生如期YYYY-mm-dd
     * @return  int 年龄
     */
    public function getAge(string $birth)
    {
        list($birthYear, $birthMonth, $birthDay) = explode('-', $birth);
        list($currentYear, $currentMonth, $currentDay) = explode('-', date('Y-m-d'));
        $age = $currentYear - $birthYear - 1;
        if ($currentMonth > $birthMonth || $currentMonth == $birthMonth && $currentDay >= $birthDay)
            $age++;
        return $age;
    }

    /**
     * 友好的时间显示
     *
     * @param int    $sTime 待显示的时间
     * @param string $type  类型. normal | mohu | full | ymd | other
     * @param string $alt   已失效
     * @return string
     */
    function friendlyDate($sTime, $type = 'mohu')
    {
        if (!$sTime)
            return '';
        //sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime      =   time();
        $dTime      =   $cTime - $sTime;
        $dDay       =   intval(date("z", $cTime)) - intval(date("z", $sTime));
        //$dDay     =   intval($dTime/3600/24);
        $dYear      =   intval(date("Y", $cTime)) - intval(date("Y", $sTime));
        //normal：n秒前，n分钟前，n小时前，日期
        if ($type == 'normal') {
            if ($dTime < 60) {
                if ($dTime < 10) {
                    return '刚刚';
                } else {
                    return intval(floor($dTime / 10) * 10) . "秒前";
                }
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
                //今天的数据.年份相同.日期相同.
            } elseif ($dYear == 0 && $dDay == 0) {
                //return intval($dTime/3600)."小时前";
                return '今天' . date('H:i', $sTime);
            } elseif ($dYear == 0) {
                return date("m月d日 H:i", $sTime);
            } else {
                return date("Y-m-d H:i", $sTime);
            }
        } elseif ($type == 'mohu') {
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dDay > 0 && $dDay <= 7) {
                return intval($dDay) . "天前";
            } elseif ($dDay > 7 &&  $dDay <= 30) {
                return intval($dDay / 7) . '周前';
            } elseif ($dDay > 30) {
                return intval($dDay / 30) . '个月前';
            }
            //full: Y-m-d , H:i:s
        } elseif ($type == 'full') {
            return date("Y-m-d , H:i:s", $sTime);
        } elseif ($type == 'ymd') {
            return date("Y-m-d", $sTime);
        } else {
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dYear == 0) {
                return date("Y-m-d H:i:s", $sTime);
            } else {
                return date("Y-m-d H:i:s", $sTime);
            }
        }
    }
    /**
     * 根据身份证号获取年龄 1-男 2-女
     * @access  public
     * @param   string  $idCard 身份证号
     * @return  int
     */
    public function idCard2sex(string $idCard)
    {
        return substr($idCard, 16, 1) % 2 == 0 ? 2 : 1;
    }
    /**
     * 获取指定日期是周几
     * @access  public
     * @param   string|int  $date   日期或时间戳
     * @return  string
     */
    public function date2week($date)
    {
        $weekArr = ['日', '一', '二', '三', '四', '五', '六'];
        if (filter_var($date, FILTER_VALIDATE_INT)) {
            return $weekArr[date('w', $date)];
        } else {
            return $weekArr[date('w', strtotime($date))];
        }
    }
}
