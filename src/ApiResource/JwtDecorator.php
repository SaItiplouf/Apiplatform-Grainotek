<?php

// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class JwtDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    )
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        // Check if the path is /auth
        $path = '/auth';
        if ($openApi->getPaths()->hasPath($path)) {
            $schemas = $openApi->getComponents()->getSchemas();

            // Add your schema modifications here for the /auth path

            // Update the existing path item for /auth if needed

            // ...

            // Return the modified OpenApi configuration
            return $openApi;
        }

        // Return the original OpenApi configuration if path is not /auth
        return $openApi;
    }
}
