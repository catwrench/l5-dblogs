<?php

namespace CatWrench\DbLogs;

use CatWrench\DbLogs\Model\Log as ModelLog;

/**
 * Class Log.
 * save log and read log.
 *
 * @method array write($bizTag, $actionTag, array $content, $operator = '', $traceKey = '')
 * @method array read($bizTag, $actionTag = '', $traceKey = '', $operator = '', $pageNum = 1, $pageSize = 15, $asc =
 *         true)
 * @method array readByTraceKey($traceKey, $pageNum = 1, $pageSize = 15, $asc = true)
 * @method array readByBizTag($bizTag, $pageNum = 1, $pageSize = 20, $asc = true)
 * @method array readByBizTraceKey($bizTag, $traceKey, $pageNum = 1, $pageSize = 20, $asc = true)
 * @method array readByOperator($operator, $bizTag = '', $pageNum = 1, $pageSize = 20, $asc = true)
 */
class Log
{
    public function test()
    {
        dd('lg5-dblogs running');
    }

    /**
     * save log content.
     *
     * @param string $bizTag
     * @param string $actionTag
     * @param array  $content
     * @param string $summary
     * @param string $operator
     * @param string $traceKey
     *
     * @return boolean
     */
    public function write($bizTag, $actionTag, array $content, $summary = '', $operator = '', $traceKey = '')
    {
        $newLog = new ModelLog();
        $newLog->biz_tag = $bizTag;
        $newLog->action_tag = $actionTag;
        $newLog->log_content = json_encode($content);
        $newLog->log_summary = $summary;
        $newLog->operator = $operator;
        $newLog->track_key = $traceKey;
        $newLog->created_date = date('Y-m-d');
        return $newLog->save();
    }

    /**
     * read log.
     *
     * @param string  $bizTag
     * @param string  $actionTag
     * @param string  $traceKey
     * @param string  $operator
     * @param integer $pageNum
     * @param integer $pageSize
     * @param boolean $asc true order by asc ｜ false order by desc
     *
     * @return array
     */
    public function read($bizTag, $actionTag = '', $traceKey = '', $operator = '', $pageNum = 1, $pageSize = 15, $asc = true)
    {
        $cond = ['biz_tag' => $bizTag];
        if (!empty($actionTag)) {
            $cond['action_tag'] = $actionTag;
        }
        if (!empty($traceKey)) {
            $cond['track_key'] = $traceKey;
        }
        if (!empty($operator)) {
            $cond['operator'] = $operator;
        }
        return $this->queryLog($cond, $pageNum, $pageSize, $asc);
    }

    /**
     * read log content by trace_key.
     *
     * @param string  $traceKey
     * @param integer $pageNum
     * @param integer $pageSize
     * @param boolean $asc asc or desc
     *
     * @return array
     */
    public function readByTraceKey($traceKey, $pageNum = 1, $pageSize = 15, $asc = true)
    {
        $cond = ['track_key' => $traceKey];
        return $this->queryLog($cond, $pageNum, $pageSize, $asc);
    }

    /**
     * read by biz_tag.
     *
     * @param string  $bizTag
     * @param integer $pageNum
     * @param integer $pageSize
     * @param boolean $asc
     *
     * @return array
     */
    public function readByBizTag($bizTag, $pageNum = 1, $pageSize = 20, $asc = true)
    {
        $cond = ['biz_tag' => $bizTag];
        return $this->queryLog($cond, $pageNum, $pageSize, $asc);
    }

    /**
     * read by biz_tag and trace_key.
     *
     * @param string  $bizTag
     * @param string  $traceKey
     * @param integer $pageNum
     * @param integer $pageSize
     * @param boolean $asc
     *
     * @return array
     */
    public function readByBizTraceKey($bizTag, $traceKey, $pageNum = 1, $pageSize = 20, $asc = true)
    {
        $cond = [
            'biz_tag'   => $bizTag,
            'track_key' => $traceKey,
        ];
        return $this->queryLog($cond, $pageNum, $pageSize, $asc);
    }

    /**
     * read by operator.
     *
     * @param string  $operator
     * @param string  $bizTag
     * @param integer $pageNum
     * @param integer $pageSize
     * @param boolean $asc
     *
     * @return array
     */
    public function readByOperator($operator, $bizTag = '', $pageNum = 1, $pageSize = 20, $asc = true)
    {
        $cond = [
            'operator' => $operator,
        ];
        if ($bizTag) {
            $cond = array_merge($cond, ['biz_tag' => $bizTag]);
        }
        return $this->queryLog($cond, $pageNum, $pageSize, $asc);
    }

    /**
     * query loq.
     *
     * @param array   $cond
     * @param integer $pageNum
     * @param integer $pageSize
     * @param boolean $asc
     *
     * @return array
     */
    protected function queryLog(array $cond, $pageNum = 1, $pageSize = 20, $asc = true)
    {
        $list = ModelLog::select(['biz_tag', 'action_tag', 'operator', 'log_content', 'log_summary', 'track_key', 'created_at', 'created_date'])
            ->where($cond)
            ->orderBy('created_at', $asc ? 'asc' : 'desc')
            ->forPage($pageNum, $pageSize)
            ->get()
            ->toArray();
        $count = ModelLog::where($cond)->count('id');

        array_walk($list, function (&$it) {
            $it['log_content'] = json_decode($it['log_content'], true);
            $it['created_date'] = date('Y-m-d', strtotime($it['created_date']));
        });
        return [
            'data'  => $list,
            'total' => $count,
        ];
    }
}
