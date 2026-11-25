<?php

namespace App\Models\advice_note;

use App\Models\Companies\CompanyAdmin;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role;

class advice_notes extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='advice_note_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'advice_note_id','company_id','user_id','exhibition_year','advice_note_status','accountant_id',
        'advice_note_description','advice_note_deadline','event','vat','invoice_type','approved_at','rejection_comment',
        'control_number','total_amount','amount_paid','balance','currency','sp_invoice_no','total_vat_amount','new_deadline','endorsement_attachment',
        'discount','discount_reasons','discount_attachment','initially_assessed_by','finally_assessed_by','rejection_reasons'
        ,'change_currency','exchange_rate','notification_date','payment_mode','discountAmount'
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ((int) $user->account_type === 1) {
            return $query;
        }

        if ((int) $user->account_type === 2) {
            return $query->whereIn('advice_notes.company_id', CompanyAdmin::where('adminId', $user->user_uuid)->select('companyId'));
        }

        return $query->whereIn('advice_notes.company_id', []);
    }

    public function isApproved(): bool
    {
        return $this->advice_note_status === 'APPROVED';
    }

    public function canDownload(User $user): bool
    {
        return $this->isApproved()
            && $user->canAny(['MANAGE_INVOICES', 'DOWNLOAD_INVOICE'], Role::class)
            && ((int) $user->account_type === 1 || $this->newQuery()->visibleTo($user)->whereKey($this->getKey())->exists());
    }

    public function isCancelable(): bool
    {
        return $this->advice_note_status !== 'CANCELED' && (float) $this->amount_paid === 0.0;
    }
}
