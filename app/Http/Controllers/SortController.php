<?php
//
//namespace App\Http\Controllers;
//
//use App\Enums\RoleEnum;
//use App\Tournament;
//use Illuminate\Http\Request;
//use Illuminate\Support\Collection;
//
//class SortController extends Controller
//{
//    const SORTABLE_FIELDS = ['id'];
//
//    const DEFAULT_COLUMN_TO_SORT_BY = 'id';
//    const DEFAULT_ORDER_TO_SORT_BY = 'desc';
//
//    const DEFAULT_PAGE_NUMBER = 1;
//    const MAX_RESULTS_PER_PAGE = 10;
//
//    public function sortTournaments(Request $request)
//    {
//        $orderToSortBy = self::DEFAULT_ORDER_TO_SORT_BY;
//        $requestOrderToSortBy = $request->get('orderToSortBy');
//
//        if ($requestOrderToSortBy && ($requestOrderToSortBy === 'asc' || $requestOrderToSortBy === 'desc')) {
//            $orderToSortBy = $requestOrderToSortBy;
//        }
//
//        $columnToSortBy = self::DEFAULT_COLUMN_TO_SORT_BY;
//        $requestColumnToSortBy = $request->get('columnToSortBy');
//        if ($requestColumnToSortBy && (array_search($requestColumnToSortBy, self::SORTABLE_FIELDS) >= 0)) {
//            $columnToSortBy = $requestColumnToSortBy;
//        }
//
//        $pageNumber = self::DEFAULT_PAGE_NUMBER;
//
//        // Check of er een pagina nummer als route parameter is meegestuurd.
//        $requestPageNumber = $request->get('pageNumber');
//
//        if ($requestPageNumber) {
//            if ((is_numeric($requestPageNumber)) && ($requestPageNumber > 0)) {
//                $pageNumber = intval($requestPageNumber);
//            }
//        }
//
//        $tournaments = Tournament::orderBy($columnToSortBy, $orderToSortBy)->get();
//
//        $lastPageNumber = ceil($tournaments->count() / self::MAX_RESULTS_PER_PAGE);
//
//        if ($pageNumber > $lastPageNumber) {
//            $pageNumber = $lastPageNumber;
//        }
//
//        $paginatedTournaments = $tournaments->forPage($pageNumber, self::MAX_RESULTS_PER_PAGE);
//        $mappedTournaments = $paginatedTournaments->map(function ($tournament) use ($authUser) {
//            $userHasOrganizerRoleForTournament = $authUser->hasRoleInTournament(RoleEnum::ORGANIZER, $tournament->id);
//            $orderedTournament['isOrganizer'] = !!$userHasOrganizerRoleForTournament;
//
//            $userHasParticipantRoleForTournament = $authUser->hasRoleInTournament(RoleEnum::PARTICIPANT, $tournament->id);
//            $orderedTournament['isOrganizer'] = !!$userHasParticipantRoleForTournament;
//
//            return $tournament;
//        });
//        return [
//            'pageNumber' => $pageNumber,
//            'lastPageNumber' => $lastPageNumber,
//            'paginatedCollection' => $paginatedTournaments,
//        ];
//    }
//}
