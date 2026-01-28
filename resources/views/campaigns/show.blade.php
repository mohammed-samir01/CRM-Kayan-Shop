<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            تفاصيل الحملة: {{ $campaign->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-medium text-gray-900">{{ $campaign->name }}</h3>
                            <div class="mt-4 space-y-3">
                                <div class="flex">
                                    <span class="text-gray-500 w-32">المنصة:</span>
                                    <span class="font-medium text-gray-900">{{ $campaign->platform }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">نوع الإعلان:</span>
                                    <span class="font-medium text-gray-900">{{ $campaign->ad_type }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">المصدر:</span>
                                    <span class="font-medium text-gray-900">{{ $campaign->source }}</span>
                                </div>
                                @if($campaign->notes)
                                    <div>
                                        <span class="text-gray-500">ملاحظات:</span>
                                        <p class="mt-1 text-gray-900">{{ $campaign->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('campaigns.edit', $campaign) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                                تعديل
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">سجل النشاط</h3>
                </div>
                <div class="p-6">
                    @forelse($activities as $activity)
                        <div class="border-b border-gray-200 pb-4 mb-4 last:border-0">
                            <div class="flex justify-between items-start">
                                <span class="font-medium text-gray-900">{{ $activity->description }}</span>
                                <span class="text-sm text-gray-500">{{ $activity->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="mt-2 text-sm text-gray-500">
                                بواسطة: {{ $activity->causer?->name ?? 'غير معروف' }}
                            </div>
                            @if(isset($activity->properties['changes']) && count($activity->properties['changes']) > 0)
                                <div class="mt-2 text-sm text-gray-700">
                                    التغييرات:
                                    <ul class="list-disc list-inside mt-1">
                                        @foreach($activity->properties['changes'] as $key => $value)
                                            <li>{{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">لا توجد سجل نشاط</p>
                    @endforelse
                    {{ $activities->links() }}
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">العملاء المتوقعين ({{ $campaign->leads->count() }})</h3>
                </div>

                <div class="p-6">
                    @forelse($campaign->leads as $lead)
                        <div class="border-b border-gray-200 py-3 last:border-0">
                            <div class="flex justify-between items-center">
                                <div>
                                    <a href="{{ route('leads.show', $lead) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        {{ $lead->lead_code }} - {{ $lead->customer_name }}
                                    </a>
                                    <div class="flex gap-2 mt-1">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ match($lead->status) {
                                                'New' => 'bg-blue-100 text-blue-800',
                                                'Contacted' => 'bg-yellow-100 text-yellow-800',
                                                'Interested' => 'bg-green-100 text-green-800',
                                                'Confirmed' => 'bg-indigo-100 text-indigo-800',
                                                'Shipped' => 'bg-purple-100 text-purple-800',
                                                'Cancelled' => 'bg-red-100 text-red-800',
                                            } }}">
                                            {{ match($lead->status) {
                                                'New' => 'جديد',
                                                'Contacted' => 'تم التواصل',
                                                'Interested' => 'مهتم',
                                                'Confirmed' => 'مؤكد',
                                                'Shipped' => 'تم الشحن',
                                                'Cancelled' => 'ملغي',
                                            } }}
                                        </span>
                                        <span class="text-sm text-gray-600">{{ number_format($lead->expected_value, 2) }} ج.م</span>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500">{{ $lead->created_at->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">لا يوجد عملاء متوقعين لهذه الحملة</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
