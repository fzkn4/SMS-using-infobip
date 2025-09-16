<?php

namespace App;

use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsRequest;
use Infobip\Model\SmsMessage;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextContent;
use Infobip\Model\SmsPreviewRequest;
use Infobip\ApiException;
use Infobip\Model\SmsDeliveryResult;
use Infobip\ObjectSerializer;

class SmsService
{
    private SmsApi $smsApi;
    private Configuration $configuration;

    public function __construct(string $apiKey, string $baseUrl = 'https://api.infobip.com')
    {
        $this->configuration = new Configuration(
            host: $baseUrl,
            apiKey: $apiKey
        );
        
        $this->smsApi = new SmsApi(config: $this->configuration);
    }

    /**
     * Send SMS message
     */
    public function sendSms(string $to, string $message, string $from = 'InfoSMS'): array
    {
        try {
            $messageObj = new SmsMessage(
                destinations: [
                    new SmsDestination(to: $to)
                ],
                content: new SmsTextContent(text: $message),
                sender: $from
            );

            $request = new SmsRequest(messages: [$messageObj]);
            $response = $this->smsApi->sendSmsMessages($request);

            return [
                'success' => true,
                'message' => 'SMS sent successfully',
                'data' => [
                    'bulkId' => $response->getBulkId(),
                    'messageCount' => count($response->getMessages() ?? []),
                    'messages' => $response->getMessages() ?? []
                ]
            ];
        } catch (ApiException $e) {
            return [
                'success' => false,
                'error' => 'Failed to send SMS: ' . $e->getMessage(),
                'details' => [
                    'code' => $e->getCode(),
                    'responseBody' => $e->getResponseBody(),
                    'responseHeaders' => $e->getResponseHeaders()
                ]
            ];
        }
    }

    /**
     * Preview SMS message (character count, parts, etc.)
     */
    public function previewSms(string $message): array
    {
        try {
            $previewRequest = new SmsPreviewRequest(text: $message);
            $response = $this->smsApi->previewSmsMessage($previewRequest);

            $previews = [];
            foreach ($response->getPreviews() ?? [] as $preview) {
                $previews[] = [
                    'charactersRemaining' => $preview->getCharactersRemaining(),
                    'textPreview' => $preview->getTextPreview(),
                    'characterCount' => $preview->getCharactersRemaining() + strlen($preview->getTextPreview()),
                    'messageCount' => $preview->getMessageCount()
                ];
            }

            return [
                'success' => true,
                'data' => $previews
            ];
        } catch (ApiException $e) {
            return [
                'success' => false,
                'error' => 'Failed to preview SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get delivery reports
     */
    public function getDeliveryReports(?string $bulkId = null, ?string $messageId = null, int $limit = 10): array
    {
        try {
            $reports = $this->smsApi->getOutboundSmsMessageDeliveryReports(
                bulkId: $bulkId,
                messageId: $messageId,
                limit: $limit
            );

            $results = [];
            foreach ($reports->getResults() ?? [] as $report) {
                $results[] = [
                    'messageId' => $report->getMessageId(),
                    'bulkId' => $report->getBulkId(),
                    'to' => $report->getTo(),
                    'status' => $report->getStatus()->getName(),
                    'statusDescription' => $report->getStatus()->getDescription(),
                    'doneAt' => $report->getDoneAt(),
                    'sentAt' => $report->getSentAt(),
                    'price' => $report->getPrice(),
                    'error' => $report->getError()
                ];
            }

            return [
                'success' => true,
                'data' => $results
            ];
        } catch (ApiException $e) {
            return [
                'success' => false,
                'error' => 'Failed to get delivery reports: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process incoming SMS webhook
     */
    public function processIncomingSms(string $rawData): array
    {
        try {
            $objectSerializer = new ObjectSerializer();
            $messages = $objectSerializer->deserialize($rawData, \Infobip\Model\SmsInboundMessageResult::class);

            $results = [];
            foreach ($messages->getResults() ?? [] as $message) {
                $results[] = [
                    'messageId' => $message->getMessageId(),
                    'from' => $message->getFrom(),
                    'to' => $message->getTo(),
                    'text' => $message->getText(),
                    'cleanText' => $message->getCleanText(),
                    'keyword' => $message->getKeyword(),
                    'receivedAt' => $message->getReceivedAt(),
                    'smsCount' => $message->getSmsCount(),
                    'price' => $message->getPrice()
                ];
            }

            return [
                'success' => true,
                'data' => $results
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to process incoming SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process delivery report webhook
     */
    public function processDeliveryReport(string $rawData): array
    {
        try {
            $objectSerializer = new ObjectSerializer();
            $deliveryResult = $objectSerializer->deserialize($rawData, SmsDeliveryResult::class);

            $results = [];
            foreach ($deliveryResult->getResults() ?? [] as $report) {
                $results[] = [
                    'messageId' => $report->getMessageId(),
                    'bulkId' => $report->getBulkId(),
                    'to' => $report->getTo(),
                    'status' => $report->getStatus()->getName(),
                    'statusDescription' => $report->getStatus()->getDescription(),
                    'doneAt' => $report->getDoneAt(),
                    'sentAt' => $report->getSentAt(),
                    'price' => $report->getPrice(),
                    'error' => $report->getError()
                ];
            }

            return [
                'success' => true,
                'data' => $results
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to process delivery report: ' . $e->getMessage()
            ];
        }
    }
}
