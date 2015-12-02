<?php
namespace Burrow\Swarrot\Processor;

use PhpAmqpLib\Exception\AMQPTimeoutException;
use Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor;
use Swarrot\Processor\ProcessorInterface;
use Psr\Log\LoggerInterface;
use Swarrot\Broker\Message;
use Burrow\Exception\ConsumerException;

class ExceptionProcessor implements ProcessorInterface
{
    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(ProcessorInterface $processor, LoggerInterface $logger = null)
    {
        $this->processor = $processor;
        $this->logger    = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function process(Message $message, array $options)
    {
        try {
            return $this->processor->process($message, $options);
        } catch (\Exception $e) {
            $this->logger and $this->logger->error(
                '[ExceptionCatcher] An exception occurred. This exception have been catch.',
                [
                    'swarrot_processor' => 'exception',
                    'exception'         => $e,
                ]
            );
            
            if ($e instanceof ConsumerException) {
                $this->logger and $this->logger->info('Closing AMqpAsyncHandler daemon...');
                return false;
            }
        }

        return;
    }
}