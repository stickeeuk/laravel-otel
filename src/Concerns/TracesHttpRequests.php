<?php

namespace PlunkettScott\LaravelOpenTelemetry\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\Context\ContextInterface;
use OpenTelemetry\Context\ScopeInterface;
use OpenTelemetry\SemConv\TraceAttributes;

trait TracesHttpRequests
{
    private SpanInterface $span;

    private ScopeInterface $scope;

    public function extractParentFromRequest(Request $request): ContextInterface
    {
        return TraceContextPropagator::getInstance()
            ->extract($request->headers->all());
    }

    public function createAndActivateRootSpan(bool $continueTrace, Request $request): void
    {
        $route = $request->route();

        $routeParameters = [];
        foreach ($route->parameterNames() as $parameterName) {
            $routeParameters[$parameterName] = $route->parameter($parameterName);
        }

        $attributes = [
            TraceAttributes::HTTP_METHOD => $request->method(),
            TraceAttributes::HTTP_URL => $request->fullUrl(),
            TraceAttributes::HTTP_ROUTE => $route->uri,
            'http.route_name' => $request->route()?->getName() ?? null,
            'http.route_parameters' => json_encode($routeParameters),
            TraceAttributes::HTTP_TARGET => $request->path(),
            TraceAttributes::HTTP_HOST => $request->getHost(),
            TraceAttributes::HTTP_SCHEME => $request->getScheme(),
            TraceAttributes::HTTP_USER_AGENT => $request->userAgent(),
            TraceAttributes::HTTP_CLIENT_IP => $request->ip(),
            TraceAttributes::HTTP_SERVER_NAME => $request->server('SERVER_NAME'),
            TraceAttributes::HTTP_REQUEST_CONTENT_LENGTH => $request->server('CONTENT_LENGTH'),
            'http.request_content_type' => $request->server('CONTENT_TYPE'),
            'http.request_content_encoding' => $request->server('CONTENT_ENCODING'),
            'http.request_accept' => $request->server('HTTP_ACCEPT'),
            'http.request_accept_encoding' => $request->server('HTTP_ACCEPT_ENCODING'),
            'http.request_accept_language' => $request->server('HTTP_ACCEPT_LANGUAGE'),
            'http.request_referer' => $request->server('HTTP_REFERER'),
            TraceAttributes::NET_HOST_IP => $request->server('SERVER_ADDR'),
            TraceAttributes::NET_HOST_PORT => $request->server('SERVER_PORT'),
            TraceAttributes::NET_PEER_IP => $request->server('REMOTE_ADDR'),
            TraceAttributes::NET_PEER_PORT => $request->server('REMOTE_PORT'),
        ];

        $spanBuilder = $this->tracer->spanBuilder($this->calculateSpanName($request))
            ->setAttributes($attributes)
            ->setSpanKind(SpanKind::KIND_SERVER);

        if ($continueTrace) {
            $spanBuilder->setParent($this->extractParentFromRequest($request));
        }

        $this->span = $spanBuilder->startSpan();
        $this->scope = $this->span->activate();
    }

    public function terminateRootSpan(Request $request, Response $response): void
    {
        $this->span->setAttributes([
            TraceAttributes::HTTP_STATUS_CODE => $response->getStatusCode(),
            TraceAttributes::HTTP_RESPONSE_CONTENT_LENGTH => $response->headers->get('Content-Length'),
            'http.response_content_type' => $response->headers->get('Content-Type'),
            'http.response_content_encoding' => $response->headers->get('Content-Encoding'),
        ]);

        $this->span->end();
        $this->scope->detach();
    }

    private function calculateSpanName(Request $request): string
    {
        $route = $request->route();
        $routeName = $route?->getName();

        if (Str::startsWith($routeName, 'generated::')) {
            // This is a route name generated by for a closure when caching
            // routes. We don't want to include these in the trace, so we'll
            // just set the name to null and default to the route path.
            $routeName = null;
        }

        $routePath = $route?->uri ?? $request->path();

        return "{$request->method()} $routePath".($routeName ? " ($routeName)" : '');
    }
}
