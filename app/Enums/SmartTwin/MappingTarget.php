<?php

namespace App\Enums\SmartTwin;

/**
 * What kind of thing a mapped result points at.
 *
 * Almost everything SmartTwin describes is an answer to a question the tool already asks, and that
 * is the default. The measures it advises are not: they are entries in the action plan, with a
 * price and no question behind them.
 *
 * The distinction lives here rather than in the applier guessing from the target string, so adding
 * a kind means adding a case and the write that belongs to it — and no mapper changes along with it.
 */
enum MappingTarget: string
{
    /** The target is a tool question short; the value is its answer. */
    case TOOL_QUESTION = 'tool-question';

    /** The target is a measure application short; the value is what the measure costs. */
    case ADVICE = 'advice';
}
