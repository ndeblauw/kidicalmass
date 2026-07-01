<x-ba-admin-layout>
    <div class="bg-white shadow overflow-hidden sm:rounded-b-lg border-t-2 border-{{$config->color ?? 'rose-500'}}">
        <div class="px-4 py-5 sm:px-6 flex justify-between bg-gray-50">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{__('Details for')}} <span class="font-bold">{{$model->name}}</span>
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    {{ __('Record of type') }} {{$config->name_to_use}}
                </p>
            </div>
            <div class="my-auto flex gap-x-2">
                @if($model->group_id)
                    <form action="{{ route('admin.contactforms.convert-to-user', $model) }}" method="POST" class="inline">
                        @csrf
                        <x-ba-admin-button type="submit" class="py-1 bg-emerald-500">
                            Convert to Interested User
                        </x-ba-admin-button>
                    </form>
                @endif
                <x-ba-delete-button action="{{$config->getDestroyUrl($model->getKey()) }}" />
            </div>
        </div>

        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4">{{ $model->name }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4">
                        <a href="mailto:{{ $model->email }}" class="text-blue-600 hover:text-blue-800">{{ $model->email }}</a>
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4">{{ $model->phone ?: '—' }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Message</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4" style="white-space: pre-wrap;">{{ $model->message ?: '—' }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Page URL</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4 break-all">{{ $model->page_url }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Group</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4">
                        @if($model->group_id)
                            <a href="{{ route('admin.groups.show', $model->group) }}" class="text-blue-600 hover:text-blue-800">{{ $model->group->name }}</a>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Handled</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4">
                        @if($model->handled_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                {{ $model->handled_at->diffForHumans() }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Unhandled</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div class="text-sm text-gray-400 px-6 float-right pb-4">
            Last updated <span class="font-bold">{{$model->updated_at->diffForHumans()}}</span> ({{$model->updated_at}})
            <span class="text-blue-300">|</span>
            Created <span class="font-bold">{{$model->created_at->diffForHumans()}}</span> ({{$model->created_at}})
        </div>
    </div>
</x-ba-admin-layout>
