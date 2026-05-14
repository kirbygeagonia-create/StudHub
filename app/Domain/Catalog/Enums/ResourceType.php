<?php

namespace App\Domain\Catalog\Enums;

enum ResourceType: string
{
    case Textbook = 'textbook';
    case EModule = 'e_module';
    case Reviewer = 'reviewer';
    case PastExam = 'past_exam';
    case LabManual = 'lab_manual';
    case ThesisSample = 'thesis_sample';
    case LectureNotes = 'lecture_notes';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Textbook => 'Textbook',
            self::EModule => 'e-Module',
            self::Reviewer => 'Reviewer',
            self::PastExam => 'Past Exam',
            self::LabManual => 'Lab Manual',
            self::ThesisSample => 'Thesis Sample',
            self::LectureNotes => 'Lecture Notes',
            self::Other => 'Other',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }
}
