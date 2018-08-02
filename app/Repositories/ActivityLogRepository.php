<?php

namespace App\Repositories;

use App\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ActivityLogRepository
{
    /**
     * @var ActivityLog 
     */
    protected $activityLog;

    /**
     * Instantiate a new Repository instance.
     *
     * @param ActivityLog $activityLog
     */
    public function __construct(ActivityLog $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /**
     * Get activity log query.
     *
     * @return ActivityLog
     */
    public function getQuery()
    {
        return $this->activityLog->with('user', 'user.profile');
    }

    /**
     * Find activity log with given id.
     *
     * @param int $id
     *
     * @return ActivityLog
     * @throws ValidationException
     */
    public function findOrFail($id)
    {
        $activityLog = $this->activityLog->find($id);

        if (!$activityLog) {
            throw ValidationException::withMessages(['message' => trans('activity.could_not_find')]);
        }

        return $activityLog;
    }

    /**
     * Paginate all activity logs using given params.
     *
     * @param array $params
     *
     * @return LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $user_id = isset($params['user_id']) ? $params['user_id'] : null;
        $created_at_start_date = isset($params['created_at_start_date']) ? $params['created_at_start_date'] : null;
        $created_at_end_date = isset($params['created_at_end_date']) ? $params['created_at_end_date'] : null;

        $query = $this->activityLog->with('user', 'user.profile')->filterByUserId($user_id)->createdAtDateBetween([
            'start_date' => $created_at_start_date,
            'end_date' => $created_at_end_date
        ]);

        return $query->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Record a new activity.
     *
     * @param array $params
     *
     * @return ActivityLog
     */
    public function record($params)
    {
        return $this->activityLog->forceCreate($this->formatParams($params));
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     *
     * @return array
     */
    private function formatParams($params)
    {
        $formatted = [
            'user_id' => isset($params['user_id']) ? $params['user_id'] : \Auth::user()->id,
            'module' => isset($params['module']) ? $params['module'] : null,
            'module_id' => isset($params['module_id']) ? $params['module_id'] : null,
            'sub_module' => isset($params['sub_module']) ? $params['sub_module'] : null,
            'sub_module_id' => isset($params['sub_moduleId']) ? $params['sub_moduleId'] : null,
            'activity' => isset($params['activity']) ? $params['activity'] : null,
            'ip' => getClientIp(),
            'user_agent' => \Request::header('User-Agent')
        ];

        return $formatted;
    }

    /**
     * Delete activity log.
     *
     * @param ActivityLog $activityLog
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(ActivityLog $activityLog)
    {
        return $activityLog->delete();
    }

    /**
     * Delete multiple activity logs.
     *
     * @param array $ids
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deleteMultiple($ids)
    {
        return $this->activityLog->whereIn('id', $ids)->delete();
    }
}
