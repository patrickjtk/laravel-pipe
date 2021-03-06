<?php

namespace Fikrimi\Pipe\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;

trait HasCreator
{
    private $autoCreator = true;

    public static function bootHasCreator()
    {
        static::creating(function ($model) {
            if ($model->autoCreator && method_exists($model, 'setCreator')) {
                $model->setCreator();
            }
        });
    }

    /**
     * @param $status
     * @return void
     */
    public function setAutoCreator($status)
    {
        $this->autoCreator = $status;

        return $this;
    }

    public function setCreator(User $user = null)
    {
        $user = $user ?: User::find(auth()->id());

        if ($user === null) {
            return $this;
        }

        $this->forceFill([
            $this->getCreatorColumn() => $user[$this->getCreatorPrimaryKey()],
        ]);

        return $this;
    }

    /**
     * @return BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, $this->getCreatorColumn(), $this->getCreatorPrimaryKey());
    }

    public function getCreatorColumn()
    {
        return 'created_by';
    }

    public function getCreatorPrimaryKey()
    {
        return 'id';
    }
}
