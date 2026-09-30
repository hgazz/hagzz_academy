<?php

namespace App\DataTables;

use App\Http\Traits\DataTablesTrait;
use App\Models\Join;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class JoinDataTable extends DataTable
{
    use DataTablesTrait;
    protected $query;

    /**
     * Set a custom query.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return static
     */
    public function with(array|string $key, mixed $value = null): static
    {
        if (is_string($key) && $key === 'query') {
            $this->query = $value;
        }

        return $this;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $ar = app()->getLocale() === 'ar';
        $academy = auth('academy')->user()?->academy ?: auth('academy')->user();
        $isGym = ($academy?->business_type === 'gym');
        $currency = $academy?->currency_symbol ?: ($ar ? 'ر.س' : 'SAR');

        return (new EloquentDataTable($query))
            ->addColumn('id_badge', function ($join) {
                return '<span class="badge bg-light text-dark border px-2 py-1 font-monospace fw-bold" style="font-size: 12px;">#' . $join->id . '</span>';
            })
            ->addColumn('member', function ($join) use ($ar) {
                $student = $join->student ?: $join->user;
                $name = e($student?->name ?: ($join->name ?: ($ar ? 'عميل غير مسجل' : 'Unknown Member')));
                $phone = e($student?->phone ?: ($join->phone ?: ''));
                $firstChar = mb_substr($name, 0, 1, 'UTF-8');
                
                $gradients = [
                    'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)',
                    'linear-gradient(135deg, #059669 0%, #10b981 100%)',
                    'linear-gradient(135deg, #d97706 0%, #f59e0b 100%)',
                    'linear-gradient(135deg, #2563eb 0%, #38bdf8 100%)',
                    'linear-gradient(135deg, #dc2626 0%, #f43f5e 100%)',
                ];
                $bgIndex = abs(crc32($name)) % count($gradients);
                $grad = $gradients[$bgIndex];

                $html = '<div class="d-flex align-items-center gap-2" style="min-width: 170px;">';
                $html .= '<div style="width: 36px; height: 36px; min-width: 36px; border-radius: 10px; background: ' . $grad . '; color: #fff; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">' . $firstChar . '</div>';
                $html .= '<div style="line-height: 1.25;">';
                $html .= '<div class="fw-bold text-dark" style="font-size: 13px;">' . $name . '</div>';
                if ($phone) {
                    $html .= '<div class="text-muted" style="font-size: 11.5px; direction: ltr; display: inline-block;"><i class="fa fa-phone-alt text-primary opacity-75 me-1" style="font-size: 10px;"></i>' . $phone . '</div>';
                }
                $html .= '</div></div>';
                return $html;
            })
            ->addColumn('training_name', function ($join) use ($isGym, $ar) {
                $training = $join->training;
                $tName = e($training?->name ?: ($isGym ? ($ar ? 'باقة عضوية' : 'Membership Plan') : ($ar ? 'برنامج تدريبي' : 'Training Plan')));
                $sport = e($training?->sport?->name ?: '');
                $coach = e($training?->coach?->name ?: '');

                $html = '<div style="min-width: 180px;">';
                $html .= '<div class="fw-semibold text-dark" style="font-size: 13px; line-height: 1.3;">' . $tName . '</div>';
                $html .= '<div class="d-flex align-items-center gap-1 flex-wrap mt-1">';
                if ($sport) {
                    $html .= '<span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 10px;"><i class="fas fa-dumbbell text-primary me-1"></i>' . $sport . '</span>';
                }
                if ($coach) {
                    $html .= '<span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 10px;"><i class="fas fa-user-tie text-secondary me-1"></i>' . $coach . '</span>';
                }
                $html .= '</div></div>';
                return $html;
            })
            ->addColumn('booking_type', function ($join) use ($ar) {
                $isOffline = ($join->invoice?->user_type === 'offline') || (bool) $join->academy_student_id;
                if ($isOffline) {
                    return '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="fas fa-store me-1"></i> ' . ($ar ? 'مباشر (النادي)' : 'Direct (Offline)') . '</span>';
                }
                return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="fas fa-mobile-screen-button me-1"></i> ' . ($ar ? 'أونلاين (تطبيق)' : 'Online App') . '</span>';
            })
            ->addColumn('financial', function ($join) use ($currency, $ar) {
                $inv = $join->invoice;
                $total = (float) ($inv?->amount ?: ($join->price ?: 0));
                $paid = (float) ($inv?->collected_amount ?: ($join->paid_amount ?: 0));
                $rem = max(0, $total - $paid);

                $html = '<div style="line-height: 1.3; min-width: 120px;">';
                $html .= '<div class="fw-bold text-dark" style="font-size: 13px;">' . number_format($total, 2) . ' <span class="text-muted fw-normal" style="font-size: 10.5px;">' . $currency . '</span></div>';
                $html .= '<div class="text-success fw-medium" style="font-size: 11.5px;">' . ($ar ? 'المسدد: ' : 'Paid: ') . number_format($paid, 2) . '</div>';
                if ($rem > 0) {
                    $html .= '<div class="text-danger fw-semibold" style="font-size: 11px;"><i class="fa fa-exclamation-circle me-1"></i>' . ($ar ? 'متبقي: ' : 'Due: ') . number_format($rem, 2) . '</div>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('payment_state', function ($join) use ($ar) {
                $inv = $join->invoice;
                $total = (float) ($inv?->amount ?: ($join->price ?: 0));
                $paid = (float) ($inv?->collected_amount ?: ($join->paid_amount ?: 0));
                $state = $inv?->payment_state ?: ($paid >= $total && $total > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'));

                if ($state === 'paid' || ($paid >= $total && $total > 0)) {
                    return '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-semibold" style="font-size: 11px;"><i class="fa fa-circle-check me-1"></i> ' . ($ar ? 'مسدد بالكامل' : 'Fully Paid') . '</span>';
                } elseif ($state === 'partial' || ($paid > 0 && $paid < $total)) {
                    return '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill fw-semibold" style="font-size: 11px;"><i class="fa fa-clock me-1"></i> ' . ($ar ? 'مسدد جزئياً' : 'Partially Paid') . '</span>';
                }
                return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill fw-semibold" style="font-size: 11px;"><i class="fa fa-circle-xmark me-1"></i> ' . ($ar ? 'غير مسدد' : 'Unpaid') . '</span>';
            })
            ->addColumn('payment_method', function ($join) use ($ar) {
                $label = $join?->invoice?->payment_method_label;
                if (!$label || $label === '-') {
                    $label = $join?->invoice?->payment_method ?: ($ar ? 'نقد / كاش' : 'Cash');
                }
                return '<span class="text-secondary fw-medium" style="font-size: 12px; white-space: nowrap;"><i class="far fa-credit-card text-muted me-1"></i> ' . e($label) . '</span>';
            })
            ->addColumn('created_at', function ($join) {
                $date = Carbon::parse($join->created_at);
                return '<div style="line-height: 1.2; min-width: 90px;">'
                    . '<div class="fw-semibold text-dark" style="font-size: 12px;">' . $date->format('Y-m-d') . '</div>'
                    . '<div class="text-muted" style="font-size: 11px;">' . $date->format('h:i A') . '</div>'
                    . '</div>';
            })
            ->addColumn('actions', function ($join) {
                return view('Academy.pages.joins.datatables.action', compact('join'))->render();
            })
            ->filterColumn('member', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('student', function ($sq) use ($keyword) {
                        $sq->where('name', 'like', "%{$keyword}%")
                           ->orWhere('phone', 'like', "%{$keyword}%");
                    })->orWhereHas('user', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', "%{$keyword}%")
                           ->orWhere('phone', 'like', "%{$keyword}%");
                    });
                });
            })
            ->filterColumn('training_name', function ($query, $keyword) {
                $query->whereHas('training', function ($tq) use ($keyword) {
                    $tq->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns([
                'id_badge',
                'member',
                'training_name',
                'booking_type',
                'financial',
                'payment_state',
                'payment_method',
                'created_at',
                'actions'
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Join $model): QueryBuilder
    {
        if ($this->query) {
            return $this->query;
        }

        $academyId = (int) (auth('academy')->user()?->academy_id ?: auth('academy')->id());
        return $model->newQuery()->with([
            'user',
            'student',
            'invoice',
            'training' => function ($query) use ($academyId) {
                $query->with(['academy', 'coach', 'sport'])
                    ->where('academy_id', $academyId);
            }
        ])->whereHas('training', function ($query) use ($academyId) {
            $query->where('academy_id', $academyId);
        });
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('join-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->scrollX(true)
            ->selectStyleSingle()
            ->dom("<'row align-items-center mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-md-end'f>>" .
                  "<'row'<'col-sm-12'tr>>" .
                  "<'row align-items-center mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>")
            ->parameters([
                'responsive'   => false,
                'autoWidth'    => false,
                'lengthMenu'   => [[10, 25, 50, 100, -1], [10, 25, 50, 100, app()->getLocale() === 'ar' ? 'الكل' : 'All']],
                'order'        => [[0, 'desc']],
                'language'     => (app()->getLocale() === 'ar') ? [
                    'url' => asset('datatableAr.json')
                ] : [
                    'url' => asset('datatableEn.json')
                ],
                'drawCallback' => 'function() {
                    if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll(\'[data-bs-toggle="tooltip"]\'));
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    }
                }'
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $ar = app()->getLocale() === 'ar';
        $academy = auth('academy')->user()?->academy ?: auth('academy')->user();
        $isGym = ($academy?->business_type === 'gym');

        return [
            ['name' => 'id', 'data' => 'id_badge', 'title' => '#', 'orderable' => true, 'searchable' => true],
            ['name' => 'member', 'data' => 'member', 'title' => $isGym ? ($ar ? 'العضو المشترك' : 'Member') : ($ar ? 'المشترك / الطالب' : 'Member / Student'), 'orderable' => false, 'searchable' => true],
            ['name' => 'training_name', 'data' => 'training_name', 'title' => $isGym ? ($ar ? 'باقة العضوية' : 'Membership Plan') : ($ar ? 'البرنامج / التدريب' : 'Training Program'), 'orderable' => false, 'searchable' => true],
            ['name' => 'booking_type', 'data' => 'booking_type', 'title' => $ar ? 'نوع الحجز' : 'Booking Type', 'orderable' => false, 'searchable' => false],
            ['name' => 'financial', 'data' => 'financial', 'title' => $ar ? 'القيمة والمسدد' : 'Amount & Paid', 'orderable' => false, 'searchable' => false],
            ['name' => 'payment_state', 'data' => 'payment_state', 'title' => $ar ? 'حالة السداد' : 'Payment Status', 'orderable' => false, 'searchable' => false],
            ['name' => 'payment_method', 'data' => 'payment_method', 'title' => $ar ? 'طريقة الدفع' : 'Payment Method', 'orderable' => false, 'searchable' => false],
            ['name' => 'created_at', 'data' => 'created_at', 'title' => $ar ? 'تاريخ التسجيل' : 'Registered At', 'orderable' => true, 'searchable' => false],
            ['name' => 'actions', 'data' => 'actions', 'title' => $ar ? 'الإجراءات' : 'Actions', 'orderable' => false, 'searchable' => false, 'exportable' => false, 'printable' => false]
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Join_' . date('YmdHis');
    }
}
