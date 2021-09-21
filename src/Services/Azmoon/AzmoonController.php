<?php

namespace Larapress\SAzmoon\Services\Azmoon;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Controller for Simple Azmoon.
 *
 * @group Simple Azmoon
 */
class AzmoonController extends Controller
{

    public static function registerPublicAPIRoutes()
    {
        Route::post('azmoon/{product_id}/details', '\\' . self::class . '@azmoonDetails')
            ->name('azmoon.any.details');
        Route::post('azmoon/{product_id}/answer_sheet', '\\' . self::class . '@acceptAzmoonAnswerSheet')
            ->name('azmoon.any.file');
    }

    public static function registerPublicWebRoutes()
    {
        Route::get('azmoon/{product_id}/question/{index}', '\\' . self::class . '@streamAzmoonQuestionFile')
            ->name('azmoon.any.question');
        Route::get('azmoon/{product_id}/answers/{index}', '\\' . self::class . '@streamAzmoonAnswerFile')
            ->name('azmoon.any.answer');
    }

    /**
     * Get Azmoon Details
     *
     * @param IAzmoonService $service
     * @param int $productId
     *
     * @return array
     */
    public function azmoonDetails(IAzmoonService $service, $productId)
    {
        return $service->getAzmoonDetails($productId);
    }

    /**
     * Stream Azmoon question file
     *
     * @param IAzmoonService $service
     * @param int $productId
     * @param int $index
     *
     * @return array
     */
    public function streamAzmoonQuestionFile(IAzmoonService $service, Request $request, $productId, $index)
    {
        return $service->streamAzmoonFileAtIndex($request, $productId, $index, false);
    }

    /**
     * Stream Azmoon answer file
     *
     * @param IAzmoonService $service
     * @param int $productId
     * @param int $index
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function streamAzmoonAnswerFile(IAzmoonService $service, Request $request, $productId, $index)
    {
        return $service->streamAzmoonFileAtIndex($request, $productId, $index, true);
    }

    /**
     * Accept user answer sheet
     *
     * @param IAzmoonService $service
     * @param Request $request
     * @param int $
     *
     * @return array
     */
    public function acceptAzmoonAnswerSheet(IAzmoonService $service, Request $request, $productId)
    {
        return $service->acceptAzmoonResultForUser($request, Auth::user(), $productId);
    }
}
