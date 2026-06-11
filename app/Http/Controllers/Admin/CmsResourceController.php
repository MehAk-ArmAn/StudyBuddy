<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CmsResourceController extends Controller
{
    public function index(Request $request, string $resource): View
    {
        $definition = $this->definition($resource);
        $query = $definition['model']::query();

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($inner) use ($definition, $search): void {
                foreach ($definition['search'] ?? [] as $column) {
                    $inner->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        foreach ($definition['order'] ?? ['id'] as $column) {
            $query->orderBy($column);
        }

        return view('admin.resources.index', [
            'resource' => $resource,
            'definition' => $definition,
            'items' => $query->paginate(20)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(string $resource): View
    {
        $definition = $this->definition($resource);
        $item = new $definition['model'];

        return view('admin.resources.form', compact('resource', 'definition', 'item'));
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $definition = $this->definition($resource);
        $data = $this->validatedData($request, $definition);

        $definition['model']::query()->create($data);

        return redirect()->route('admin.resources.index', $resource)->with('status', $definition['label']);
    }

    public function edit(string $resource, int $id): View
    {
        $definition = $this->definition($resource);
        $item = $definition['model']::query()->findOrFail($id);

        return view('admin.resources.form', compact('resource', 'definition', 'item'));
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $definition = $this->definition($resource);
        $item = $definition['model']::query()->findOrFail($id);
        $data = $this->validatedData($request, $definition, $item);

        if ($item instanceof AdminUser && array_key_exists('is_active', $data) && ! $data['is_active'] && $this->isLastActiveAdmin($item)) {
            return back()->withErrors(['is_active' => ''])->withInput();
        }

        $item->update($data);

        return redirect()->route('admin.resources.index', $resource)->with('status', $definition['label']);
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $definition = $this->definition($resource);
        $item = $definition['model']::query()->findOrFail($id);

        if ($item instanceof AdminUser && $this->isLastActiveAdmin($item)) {
            return back()->withErrors(['delete' => '']);
        }

        $item->delete();

        return redirect()->route('admin.resources.index', $resource)->with('status', $definition['label']);
    }

    private function definition(string $resource): array
    {
        abort_unless(array_key_exists($resource, config('admin_cms.resources')), 404);

        return config("admin_cms.resources.{$resource}");
    }

    private function validatedData(Request $request, array $definition, ?Model $item = null): array
    {
        $rules = collect($definition['fields'])->mapWithKeys(function (array $field, string $name) use ($definition, $item): array {
            $rules = Arr::wrap($field['rules']);

            if (in_array($name, ['key', 'slug', 'email', 'path'], true)) {
                $rules[] = Rule::unique((new $definition['model'])->getTable(), $name)->ignore($item?->getKey());
            }

            if (($field['type'] ?? '') === 'password' && $item?->exists) {
                $rules = array_values(array_filter($rules, fn ($rule): bool => $rule !== 'required'));
            }

            return [$name => $rules];
        })->all();

        $data = $request->validate($rules);

        foreach ($definition['fields'] as $name => $field) {
            if (($field['type'] ?? '') === 'boolean') {
                $data[$name] = $request->boolean($name);
            }

            if (($field['type'] ?? '') === 'password') {
                if (filled($data[$name] ?? null)) {
                    $data[$name] = Hash::make($data[$name]);
                } else {
                    unset($data[$name]);
                }
            }
        }

        return $data;
    }

    private function isLastActiveAdmin(AdminUser $admin): bool
    {
        return $admin->is_active && AdminUser::query()->where('is_active', true)->whereKeyNot($admin->getKey())->doesntExist();
    }
}
