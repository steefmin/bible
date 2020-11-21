<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Line;
use App\Repository\BookRepository;
use App\Repository\LineRepository;
use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

class SubtitleImporter
{
    /** @var LineRepository */
    private $lineRepository;

    /** @var BookRepository */
    private $bookRepository;

    public function __construct(
        LineRepository $lineRepository,
        BookRepository $bookRepository
    ) {
        $this->lineRepository = $lineRepository;
        $this->bookRepository = $bookRepository;
    }

    public function import(\SimpleXMLElement $contents): array
    {
        $xml = $contents->body->div;

        $lines = [];
        $n = 1;
        foreach ($xml->children() as $number => $line) {
            $lineData = $this->parseChild($line);

            if (trim($lineData['text']) === '') {
                continue;
            }

            $lines[] = array_merge($lineData, ['number' => $n++]);
        }

        return $lines;
    }

    private function parseChild(\SimpleXMLElement $line): array
    {
        $keepKeys = [
            'text' => null,
            'time' => null,
            'number' => null,
        ];

        $lineText = '';
        foreach ($line as $span) {
            if ($span === '') {
                continue;
            }
            if ($this->isAudioDescriptionLine((string) $span)) {
                continue;
            }
            $lineText .= ' ' . $span;
        }

        $attributes = [
            'text' => str_replace('  ', ' ', $lineText),
        ];

        foreach ($line->attributes() as $key => $value) {
            $attributes[$key] = (string) $value;
        }

        $attributes['time'] = (int) (((int) $attributes['begin']) / 10000000);

        return array_intersect_key($attributes, $keepKeys);
    }

    public function commit(array $lines, int $bookId): void
    {
        $book = $this->bookRepository->find($bookId);

        $entities = [];
        foreach ($lines as $line) {
            $lineEntity = new Line();
            $lineEntity->setBook($book);
            $lineEntity->setNumber($line['number']);
            $lineEntity->setText($line['text']);
            $lineEntity->setTime($line['time']);
            $entities[] = $lineEntity;
        }

        $this->lineRepository->persistArrayOf($entities);
    }

    private function isAudioDescriptionLine($text)
    {
        return preg_match('/^\[.*]$/', trim($text));
    }
}
