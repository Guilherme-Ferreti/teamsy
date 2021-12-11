<?php

namespace App\Http\Livewire;

use App\Models\Department;
use Livewire\Component;

class DepartmentForm extends Component
{
    public string $name = 'Accounting';
    public bool $success = false;

    public function mount($department_id = null)
    {
        if ($department_id) {
            $this->name = Department::findOrFail($department_id)->name;
        }
    }

    public function submit()
    {
        Department::create([
            'name' => $this->name,
        ]);
        
        $this->success = true;
    }
    
    public function render()
    {
        return view('livewire.department-form');
    }
}
