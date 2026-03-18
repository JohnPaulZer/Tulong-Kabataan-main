<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Campaign;

class CampaignsGrid extends Component
{
    use WithPagination;

    public $sort = 'latest';
    public $perPage = 12;
    public $campaignCount = 0;

    public $pollingInterval = 2000;

    public function mount()
    {
        $this->updateCampaignCount();
    }

    public function updatedSort($value)
    {
        $this->resetPage();
    }

    public function updatedPerPage($value)
    {
        $this->resetPage();
    }

    public function updateCampaignCount()
    {
        $this->campaignCount = Campaign::where('status', 'active')->count();
    }

    public function render()
    {
        // Update count on every render (triggered by polling)
        $this->updateCampaignCount();

        $query = Campaign::where('status', 'active');

        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_funded':
                $query->orderBy('current_amount', 'desc');
                break;
            case 'ending_soon':
                $query->orderBy('ends_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $campaigns = $query->paginate($this->perPage);

        return view('livewire.campaigns-grid', [
            'campaigns' => $campaigns,
        ]);
    }
}
