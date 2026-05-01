<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Quote;
use App\Models\Testimonial;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $monthStart = now()->startOfMonth();
        $nextMonthStart = (clone $monthStart)->addMonth();

        $quoteStats = Quote::query()
            ->selectRaw('COUNT(*) as total_quotes')
            ->selectRaw("SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_quotes")
            ->selectRaw('SUM(CASE WHEN created_at >= ? AND created_at < ? THEN 1 ELSE 0 END) as this_month_quotes', [
                $monthStart,
                $nextMonthStart,
            ])
            ->first();

        $newQuotes = (int) ($quoteStats?->new_quotes ?? 0);
        $totalQuotes = (int) ($quoteStats?->total_quotes ?? 0);
        $thisMonthQuotes = (int) ($quoteStats?->this_month_quotes ?? 0);
        $totalProjects = Project::where('is_active', true)->count();
        $testimonials = Testimonial::where('is_active', true)->count();

        return [
            Stat::make('Nouvelles demandes', $newQuotes)
                ->description('En attente de traitement')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('danger')
                ->chart([7, 3, 4, 5, 6, $newQuotes])
                ->url(route('filament.admin.resources.quotes.index', ['tableFilters[status][value]' => 'new'])),

            Stat::make('Demandes ce mois', $thisMonthQuotes)
                ->description('Total: ' . $totalQuotes . ' demandes')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([2, 4, 6, 8, 5, $thisMonthQuotes]),

            Stat::make('Projets actifs', $totalProjects)
                ->description('Dans le portfolio')
                ->descriptionIcon('heroicon-m-photo')
                ->color('success'),

            Stat::make('Témoignages', $testimonials)
                ->description('Avis clients publiés')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
}
