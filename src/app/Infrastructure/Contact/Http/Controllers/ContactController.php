<?php

namespace App\Infrastructure\Contact\Http\Controllers;

use App\Application\Contact\UseCases\CreateContactUseCase;
use App\Application\Contact\UseCases\DeleteContactUseCase;
use App\Application\Contact\UseCases\GetContactUseCase;
use App\Application\Contact\UseCases\ListContactsUseCase;
use App\Application\Contact\UseCases\UpdateContactUseCase;
use App\Infrastructure\Contact\Http\Requests\StoreContactRequest;
use App\Infrastructure\Contact\Http\Requests\UpdateContactRequest;
use App\Infrastructure\Contact\Http\Resources\ContactResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ContactController extends Controller
{
    public function store(
        StoreContactRequest $request,
        CreateContactUseCase $useCase
    ): JsonResponse {
        $contact = $useCase->execute(
            name: $request->validated('name'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
        );

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    public function index(
        Request $request,
        ListContactsUseCase $useCase
    ): JsonResponse {
        $result = $useCase->execute(
            perPage: $request->integer('per_page', 15)
        );

        $paginator = $result['paginator'];
        $resources = collect($result['data'])->map(fn ($c) => (new ContactResource($c))->resolve());

        return response()->json([
            'data' => $resources,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    public function show(
        int $contact,
        GetContactUseCase $useCase
    ): ContactResource {
        return new ContactResource($useCase->execute($contact));
    }

    public function update(
        UpdateContactRequest $request,
        int $contact,
        UpdateContactUseCase $useCase
    ): ContactResource {
        $updated = $useCase->execute(
            id: $contact,
            name: $request->validated('name'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
        );

        return new ContactResource($updated);
    }

    public function destroy(
        int $contact,
        DeleteContactUseCase $useCase
    ): JsonResponse {
        $useCase->execute($contact);

        return response()->json(null, 204);
    }
}
