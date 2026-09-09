<?php

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Enums\LessonType;

/**
 * Demonstration catalogue for Oacer Academy.
 *
 * Replace this file's content with the academy's real programmes; the seeder
 * reads it verbatim, so no code has to change when the curriculum does.
 */
return [

    [
        'slug' => 'performance-marketing-foundations',
        'title' => 'Performance Marketing Foundations',
        'category' => 'marketing',
        'instructor' => 'priya-raman',
        'price' => 12900,
        'compare_at' => 16900,
        'level' => CourseLevel::Beginner,
        'featured' => true,
        'short' => 'Plan, launch and defend a paid acquisition programme — from market sizing through to the spend review you have to sit in.',
        'description' => "Most paid media training teaches you the buttons. This programme teaches the decisions.\n\nOver six weeks you will build a complete acquisition plan for one business: sized honestly, budgeted against a real contribution margin, structured so that a bad month tells you something useful instead of causing panic. You will work in the same order I do with clients — market, offer, channel, creative, measurement — and finish with a testing calendar you can actually run.\n\nEvery module ends with a worksheet you complete for your own business or a case we provide. The live sessions are for the parts that need a conversation: reading a poor result honestly, and saying no to a channel your founder is attached to.",
        'outcomes' => [
            'Size a realistic addressable market without inflating it',
            'Set a channel budget from contribution margin rather than gut feel',
            'Structure campaigns so results stay readable as spend scales',
            'Build a 90-day testing calendar with clear stop rules',
            'Read a underperforming account and decide what to change first',
            'Present a spend review that survives finance-team questions',
        ],
        'requirements' => [
            'Access to an ad account, or use the case study we provide',
            'Comfort with spreadsheets — formulas, not modelling',
            'No previous paid media experience required',
        ],
        'audience' => [
            'Marketers taking ownership of paid spend for the first time',
            'Founders who currently buy media themselves',
            'Agency staff moving from execution into planning',
        ],
        'modules' => [
            [
                'title' => 'Before you spend anything',
                'description' => 'The arithmetic that determines whether paid acquisition can work for this business at all.',
                'lessons' => [
                    ['title' => 'What this course is and is not', 'minutes' => 7, 'preview' => true, 'description' => 'How the six weeks are structured, what you will produce, and the one habit that matters more than any tactic.'],
                    ['title' => 'Contribution margin, honestly calculated', 'minutes' => 21, 'preview' => true, 'description' => 'Working out what you can actually afford to pay for a customer, including the costs teams routinely leave out.'],
                    ['title' => 'Sizing a market you can reach', 'minutes' => 26, 'description' => 'Separating the market that exists from the market you can address with the budget in front of you.', 'resource' => [
                        'title' => 'Market sizing worksheet',
                        'file' => 'market-sizing-worksheet.pdf',
                        'body' => "Work through each section for your own business before the next lesson. Bring the completed sheet to the first live session.\n\n1. Define the buying population precisely. Who has the problem, the budget and the authority?\n2. Estimate reachable population per channel. Not the total market: the share you can put an advert in front of this quarter.\n3. Apply a realistic conversion band. Use your own data where it exists; use the benchmark range in Module 2 where it does not.\n4. Multiply by contribution margin to get a defensible ceiling on spend.\n5. Write one sentence explaining why the number could be wrong.",
                    ]],
                    ['title' => 'When paid media is the wrong answer', 'minutes' => 14, 'description' => 'Three business shapes where spending money on ads reliably destroys value, and what to do instead.'],
                ],
            ],
            [
                'title' => 'Structure that stays readable',
                'description' => 'Account architecture, naming and budget allocation that still make sense at ten times the spend.',
                'lessons' => [
                    ['title' => 'Campaign architecture principles', 'minutes' => 24, 'description' => 'Why most accounts become unreadable, and the structure that prevents it.'],
                    ['title' => 'A naming convention you will keep', 'minutes' => 12, 'description' => 'Boring, strict, and the single highest-leverage thirty minutes of setup work you will do.', 'resource' => [
                        'title' => 'Naming convention reference',
                        'file' => 'naming-convention.pdf',
                        'body' => "Pattern: [channel]_[objective]_[audience]_[offer]_[variant]\n\nRules that matter:\n- Lower case only. No spaces. Underscores between segments, hyphens within one.\n- Never encode a date. Dates belong in the platform, not the name.\n- Never encode a budget. Budgets change; names should not.\n- Every segment must be a value from a fixed list you keep in the shared sheet.\n\nWhy: reporting stops depending on anyone remembering what a campaign was for.",
                    ]],
                    ['title' => 'Splitting budget across the funnel', 'minutes' => 28, 'description' => 'How much goes to demand capture versus demand creation, and how that ratio should move over a year.'],
                    ['title' => 'Setting stop rules in advance', 'minutes' => 18, 'description' => 'Deciding what failure looks like before you are emotionally invested in the result.'],
                ],
            ],
            [
                'title' => 'Testing and reading results',
                'description' => 'Running a testing calendar that produces decisions rather than dashboards.',
                'lessons' => [
                    ['title' => 'The 90-day testing calendar', 'minutes' => 25, 'description' => 'Sequencing tests so that each one is still interpretable once the next begins.'],
                    ['title' => 'Significance without the statistics degree', 'minutes' => 22, 'description' => 'A practical threshold for calling a result, and the three ways teams fool themselves.'],
                    ['title' => 'Reading an account that is underperforming', 'minutes' => 31, 'description' => 'A diagnostic order of operations: audience, offer, creative, landing, tracking.'],
                    ['title' => 'Writing the spend review', 'type' => LessonType::Text, 'minutes' => 15, 'description' => 'The one-page format I use with finance teams.', 'content' => "A spend review has one job: to let someone who was not in the room decide whether to continue funding this.\n\nUse one page, four sections.\n\n**What we spent and what it returned.** Two numbers, the period, and the comparison period. No commentary yet.\n\n**What we learned.** Two or three findings, each stated as a decision it changes. \"Retargeting audiences below 30 days outperform 90-day windows by 40%, so we are narrowing the window\" — not \"retargeting performed well\".\n\n**What we are changing next period.** A short list. Each item names an owner and a date.\n\n**What would make us stop.** The condition under which you would recommend cutting this budget yourself. Writing this down is what makes the rest of the document credible."],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: reviewing your market sizing', 'description' => 'Bring your completed worksheet. We will work through four of them in detail and pressure-test the assumptions.', 'preparation' => 'Complete the market sizing worksheet from Module 1 and have your contribution margin figure to hand.', 'minutes' => 75],
            ['title' => 'Live: account teardown clinic', 'description' => 'Submit an account in advance and we will diagnose it together using the order of operations from Module 3.', 'preparation' => 'Submit read-only access or an anonymised export by the Friday before.', 'minutes' => 90],
            ['title' => 'Live: the awkward spend conversation', 'description' => 'Rehearsing the conversation where you recommend cutting a channel someone senior is attached to.', 'past' => true, 'minutes' => 60],
        ],
        'faqs' => [
            ['Do I need my own ad account?', 'It helps, but no. We provide an anonymised case study with real data that you can use for every worksheet in the course.'],
            ['Which platforms does this cover?', 'The principles are platform-agnostic. Worked examples use Google Ads and Meta because that is where most budgets sit.'],
            ['How much time per week should I plan for?', 'Roughly three hours: two of lessons and one of applying the worksheet to your own business.'],
        ],
    ],

    [
        'slug' => 'brand-identity-design-studio',
        'title' => 'Brand Identity Design Studio',
        'category' => 'design',
        'instructor' => 'daniel-okoro',
        'price' => 14900,
        'level' => CourseLevel::Intermediate,
        'featured' => true,
        'short' => 'Take an identity from first conversation to a documented system another designer can build on without asking you questions.',
        'description' => "This is a studio course, not a theory course. You will run one identity project from brief to handover, and I will critique it at three points along the way.\n\nThe emphasis throughout is on decisions you can defend. A logo that you can explain is worth more to a client than a beautiful one you cannot. We will spend as much time on the document that carries the work forward as on the marks themselves — because that document is what determines whether the identity survives contact with a marketing team.\n\nYou need to be able to use your tool of choice competently. I will not teach software.",
        'outcomes' => [
            'Run a discovery conversation that produces usable constraints',
            'Develop a mark through deliberate iteration rather than volume',
            'Build a type and colour system with defined roles, not just values',
            'Document an identity so a developer can implement it correctly',
            'Present work in a way that reduces revision rounds',
        ],
        'requirements' => [
            'Competent with a vector tool (Figma, Illustrator or Affinity)',
            'A real or invented client brief to work against',
            'Roughly five hours a week for studio work',
        ],
        'audience' => [
            'Designers moving from execution into identity work',
            'Freelancers who want fewer revision rounds',
            'In-house designers responsible for a rebrand',
        ],
        'modules' => [
            [
                'title' => 'Discovery that produces constraints',
                'description' => 'Turning a vague client conversation into decisions you can design against.',
                'lessons' => [
                    ['title' => 'How this studio runs', 'minutes' => 6, 'preview' => true, 'description' => 'The project you will complete, the three critique points, and what to have ready before Module 2.'],
                    ['title' => 'The discovery conversation', 'minutes' => 27, 'preview' => true, 'description' => 'Twelve questions that surface real constraints, and the three that clients answer dishonestly.'],
                    ['title' => 'Writing the design brief', 'minutes' => 19, 'description' => 'Converting the conversation into a one-page brief the client signs before you draw anything.', 'resource' => [
                        'title' => 'Design brief template',
                        'file' => 'design-brief-template.pdf',
                        'body' => "One page. If it runs to two, the project is not scoped.\n\nThe business in one sentence. Written by the client, edited by you.\n\nWho this has to work for. Name the primary audience and one audience it does not need to serve.\n\nWhere it will appear. List the three contexts that matter most. These constrain the mark more than any adjective will.\n\nWhat must be true. Non-negotiables: an existing colour, a legal requirement, a parent brand relationship.\n\nWhat we are deliberately not doing. The most useful line on the page.\n\nHow we will know it worked. Agreed before the work starts, not after.",
                    ]],
                ],
            ],
            [
                'title' => 'The mark',
                'description' => 'Deliberate iteration: fewer directions, explored properly.',
                'lessons' => [
                    ['title' => 'Three directions, not thirty', 'minutes' => 23, 'description' => 'Why volume of concepts signals uncertainty, and how to choose the three worth developing.'],
                    ['title' => 'Drawing for reproduction', 'minutes' => 34, 'description' => 'Optical correction, minimum sizes and the failure modes that only appear in production.'],
                    ['title' => 'Testing a mark honestly', 'minutes' => 20, 'description' => 'A checklist that catches most of the problems clients find after signoff.', 'resource' => [
                        'title' => 'Mark stress-test checklist',
                        'file' => 'mark-stress-test.pdf',
                        'body' => "Run every direction through this before you present it.\n\nScale: legible at 16px and at three metres.\nSingle colour: works in pure black and pure white, no gradients relied on.\nCrop: still recognisable when partially obscured, as it will be by app icons and avatars.\nMotion: has an obvious way to animate that does not require redrawing it.\nAdjacency: sits beside a competitor mark without reading as derivative.\nMisreading: say the name aloud to someone who has not seen it. Note anything they misread.\nEmbroidery and etching: survives the two production methods that punish detail hardest.",
                    ]],
                ],
            ],
            [
                'title' => 'The system and the handover',
                'description' => 'Type, colour and the document that lets other people use your work correctly.',
                'lessons' => [
                    ['title' => 'Type as a system of roles', 'minutes' => 29, 'description' => 'Defining what each face and weight is for, so the next person does not have to guess.'],
                    ['title' => 'Colour with defined jobs', 'minutes' => 25, 'description' => 'Building a palette around function and contrast rather than mood boards.'],
                    ['title' => 'Writing guidelines developers will read', 'minutes' => 31, 'description' => 'Tokens, spacing scales and the handful of rules that prevent drift.'],
                    ['title' => 'Presenting without over-explaining', 'minutes' => 18, 'description' => 'Structuring the presentation so the client decides rather than reacts.'],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: brief and direction critique', 'description' => 'Present your brief and three directions. Honest, specific critique in front of the group.', 'preparation' => 'Upload your brief and directions as a single PDF 48 hours in advance.', 'minutes' => 90],
            ['title' => 'Live: systems and handover review', 'description' => 'We review your documentation as a developer would — looking for the gaps that cause implementation drift.', 'minutes' => 75],
        ],
        'faqs' => [
            ['Do I need a real client?', 'No. An invented brief works, and we supply two if you would rather not invent one. Real briefs get better critique.'],
            ['Which software do you teach?', 'None. Use whatever you are fluent in. The critique is on the decisions, not the tool.'],
        ],
    ],

    [
        'slug' => 'financial-modelling-for-founders',
        'title' => 'Financial Modelling for Founders',
        'category' => 'business',
        'instructor' => 'anita-fernandes',
        'price' => 15900,
        'level' => CourseLevel::Intermediate,
        'featured' => true,
        'short' => 'Build one operating model you actually trust, and learn to change an assumption in front of an investor without losing your footing.',
        'description' => "Founders do not need more finance theory. They need one model, built properly, that they understand well enough to defend.\n\nBy the end of this course you will have a three-statement operating model for your own business, driven by assumptions you can justify, with a scenario layer that lets you answer \"what if\" questions live in a meeting. We build it together, cell by cell, in a spreadsheet — no proprietary tools.\n\nI will also show you the four mistakes I see in almost every founder-built model, and how investors spot them within ninety seconds.",
        'outcomes' => [
            'Build a driver-based three-statement model from a blank sheet',
            'Separate assumptions from calculations so the model stays auditable',
            'Add a scenario layer you can switch live in a meeting',
            'Model runway and hiring plans that survive scrutiny',
            'Answer investor questions by changing an input, not by promising to follow up',
        ],
        'requirements' => [
            'Working knowledge of a spreadsheet application',
            'Your own revenue and cost figures, or use our sample company',
            'No accounting background required',
        ],
        'audience' => [
            'Founders preparing to raise',
            'Early operators who inherited a model they do not trust',
            'Anyone presenting numbers to a board for the first time',
        ],
        'modules' => [
            [
                'title' => 'Foundations',
                'description' => 'Structure first: where assumptions live and why that decision governs everything after it.',
                'lessons' => [
                    ['title' => 'What a good model is for', 'minutes' => 11, 'preview' => true, 'description' => 'Models are decision tools, not forecasts. What follows from taking that seriously.'],
                    ['title' => 'Sheet structure and colour discipline', 'minutes' => 18, 'preview' => true, 'description' => 'The layout convention that makes a model auditable by someone who did not build it.'],
                    ['title' => 'Driver-based thinking', 'minutes' => 26, 'description' => 'Building revenue from the handful of things you can actually influence.', 'resource' => [
                        'title' => 'Model structure conventions',
                        'file' => 'model-conventions.pdf',
                        'body' => "Follow these and any competent reader can audit your model in twenty minutes.\n\nColour: blue for hard-coded inputs, black for formulas, green for links to another sheet. Never hard-code inside a formula.\n\nSheets in order: Cover, Assumptions, Workings, P&L, Balance Sheet, Cash Flow, Outputs. One purpose per sheet.\n\nTime runs left to right, one column per period, consistently across every sheet. Never break the column grid.\n\nOne formula per row, copied across. If a row needs an exception, it needs its own row.\n\nNo circular references. If you think you need one, you need an iteration schedule instead.\n\nEvery assumption gets a source note in the adjacent column. \"Founder estimate\" is an acceptable source. Blank is not.",
                    ]],
                ],
            ],
            [
                'title' => 'The three statements',
                'description' => 'Profit and loss, balance sheet and cash flow, linked properly.',
                'lessons' => [
                    ['title' => 'Building the P&L', 'minutes' => 33, 'description' => 'Revenue, direct costs and the operating expense structure that reflects how you actually hire.'],
                    ['title' => 'The balance sheet that balances', 'minutes' => 29, 'description' => 'Working capital, fixed assets and the check row that tells you immediately when something broke.'],
                    ['title' => 'Cash flow and runway', 'minutes' => 27, 'description' => 'Indirect method, and reading runway honestly rather than optimistically.'],
                    ['title' => 'The four mistakes investors spot first', 'type' => LessonType::Text, 'minutes' => 12, 'description' => 'What gets a model dismissed in the first ninety seconds.', 'content' => "**Revenue that grows by a percentage.** If your growth line is \"15% month on month\" with no driver underneath it, you have written a wish. Build revenue from customers, price and churn, and let the percentage be an output.\n\n**Costs that do not respond to growth.** A model where headcount is flat while revenue triples tells the reader you have not thought about delivery. Tie the roles that scale to the thing that drives them.\n\n**A hiring plan with no start dates.** Salaries entered as annual totals hide the timing that determines runway. One row per role, with a start month.\n\n**No scenario layer.** If the answer to \"what if churn is double\" is \"I will send that through tomorrow\", the meeting is over. A switch cell and three assumption columns take an hour to build and change how the conversation goes."],
                ],
            ],
            [
                'title' => 'Scenarios and the meeting',
                'description' => 'Making the model useful in the room, under questioning.',
                'lessons' => [
                    ['title' => 'Building the scenario switch', 'minutes' => 24, 'description' => 'Base, downside and upside driven from one cell, without duplicating the model.'],
                    ['title' => 'Sensitivity tables that mean something', 'minutes' => 21, 'description' => 'Choosing the two variables worth testing and presenting the grid readably.'],
                    ['title' => 'Presenting numbers under pressure', 'minutes' => 23, 'description' => 'Sequencing the walkthrough, and what to say when you do not know.'],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: model surgery', 'description' => 'Bring your model. We will open it and fix the structural problems together.', 'preparation' => 'Share your file 24 hours ahead. Anonymise figures if you prefer.', 'minutes' => 90],
            ['title' => 'Live: investor question drill', 'description' => 'I ask the questions investors ask. You answer by changing your model live.', 'minutes' => 60],
        ],
        'faqs' => [
            ['Excel or Google Sheets?', 'Either. Every formula shown works in both, and the downloadable template comes in both formats.'],
            ['Is this useful pre-revenue?', 'Yes, and arguably more so. We spend a full lesson on building a defensible model before you have historical data.'],
        ],
    ],

    [
        'slug' => 'conversion-copywriting',
        'title' => 'Conversion Copywriting',
        'category' => 'marketing',
        'instructor' => 'priya-raman',
        'price' => 8900,
        'level' => CourseLevel::Beginner,
        'short' => 'Write landing pages, emails and ads that people finish reading — using research rather than formulas.',
        'description' => "Copy that converts is mostly research wearing a nice sentence.\n\nThis course spends its first third on gathering the raw material — customer interviews, review mining, support tickets — because that is where the actual words come from. The rest is structure: how a landing page should be sequenced, why most email sequences lose people in the second message, and how to edit your own work without flattening it.\n\nYou will write and rewrite one landing page across the course, and get it critiqued twice.",
        'outcomes' => [
            'Run a customer interview that produces usable language',
            'Mine reviews and tickets systematically for objections',
            'Sequence a landing page around a single decision',
            'Write an email sequence that survives past message two',
            'Edit your own copy without sanding off what made it work',
        ],
        'requirements' => [
            'No copywriting experience needed',
            'A product, service or offer to write about',
        ],
        'audience' => [
            'Marketers who inherited the writing',
            'Founders writing their own site copy',
            'Designers who keep receiving bad copy to lay out',
        ],
        'modules' => [
            [
                'title' => 'Research is the work',
                'lessons' => [
                    ['title' => 'Why formulas stop working', 'minutes' => 9, 'preview' => true, 'description' => 'What AIDA and PAS are actually for, and why leaning on them produces interchangeable pages.'],
                    ['title' => 'The customer interview', 'minutes' => 28, 'description' => 'Eight questions, and the discipline of shutting up after asking them.'],
                    ['title' => 'Mining reviews and tickets', 'minutes' => 22, 'description' => 'A repeatable process for turning existing text into an objection list ranked by frequency.', 'resource' => [
                        'title' => 'Message mining sheet',
                        'file' => 'message-mining-sheet.pdf',
                        'body' => "Collect at least forty pieces of raw customer language before you write a line.\n\nSources, in order of usefulness: recorded sales calls, support tickets, cancellation reasons, your own reviews, competitor reviews at three stars.\n\nFor each item record: the exact words used, the emotion behind it, the stage of the journey, and whether it is a benefit, an objection or a comparison.\n\nThen count. The objection that appears most often belongs above the fold, not in an FAQ at the bottom.\n\nRule: if a phrase in your final copy does not trace back to a row on this sheet, you invented it. Sometimes that is fine. Know when you are doing it.",
                    ]],
                ],
            ],
            [
                'title' => 'Structure',
                'lessons' => [
                    ['title' => 'One page, one decision', 'minutes' => 24, 'description' => 'Sequencing a landing page around the single thing you are asking the reader to decide.'],
                    ['title' => 'Headlines that are not clever', 'minutes' => 17, 'description' => 'Specificity beats wordplay. Six patterns and when each applies.'],
                    ['title' => 'Handling objections in order', 'minutes' => 21, 'description' => 'Placing each objection where the reader actually raises it.'],
                    ['title' => 'Email sequences past message two', 'minutes' => 26, 'description' => 'Why open rates collapse at the second email and how to structure so they do not.'],
                ],
            ],
            [
                'title' => 'Editing',
                'lessons' => [
                    ['title' => 'Cutting without flattening', 'minutes' => 19, 'description' => 'Editing passes in a fixed order, each with one job.'],
                    ['title' => 'Reading your copy aloud', 'minutes' => 11, 'description' => 'The cheapest quality check there is, and what to listen for.'],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: landing page critique', 'description' => 'Submit your page. We work through as many as time allows, in public.', 'preparation' => 'Submit a link or PDF by the Friday before.', 'minutes' => 75],
        ],
        'faqs' => [
            ['Do I need existing customers to interview?', 'It is much better if you do. If not, the review-mining lesson gives you a workable substitute using competitor reviews.'],
        ],
    ],

    [
        'slug' => 'data-storytelling-with-spreadsheets',
        'title' => 'Data Storytelling with Spreadsheets',
        'category' => 'analytics',
        'instructor' => 'anita-fernandes',
        'price' => 6900,
        'level' => CourseLevel::Beginner,
        'short' => 'Turn a messy export into a chart that changes someone\'s mind — without leaving your spreadsheet.',
        'description' => "You do not need a data team to make a good argument with numbers.\n\nThis short course covers cleaning a real export, choosing the chart that fits the claim you are making, and presenting it so the decision is obvious. We work entirely in spreadsheets, because that is where most of these arguments actually get made.\n\nIt is deliberately short. Four hours, and you will be measurably better at the thing.",
        'outcomes' => [
            'Clean a messy export reliably and repeatably',
            'Choose a chart type from the claim rather than from habit',
            'Strip a chart down to what carries the argument',
            'Present a finding so the decision is obvious',
        ],
        'requirements' => ['Basic spreadsheet familiarity'],
        'audience' => [
            'Anyone who presents numbers to colleagues',
            'Operators without an analyst to lean on',
        ],
        'modules' => [
            [
                'title' => 'Getting the data usable',
                'lessons' => [
                    ['title' => 'What this course covers', 'minutes' => 5, 'preview' => true, 'description' => 'Four hours, three modules, one worked example from export to final slide.'],
                    ['title' => 'Cleaning an export without breaking it', 'minutes' => 26, 'description' => 'A repeatable sequence that leaves an audit trail.'],
                    ['title' => 'Shaping data for charting', 'minutes' => 22, 'description' => 'Long versus wide, and why the wrong shape makes the right chart impossible.'],
                ],
            ],
            [
                'title' => 'Choosing the chart',
                'lessons' => [
                    ['title' => 'Start from the claim', 'minutes' => 18, 'description' => 'Comparison, composition, distribution, relationship, change over time — pick the form from the sentence.'],
                    ['title' => 'Charts that mislead by accident', 'minutes' => 20, 'description' => 'Truncated axes, dual axes and the pie chart problem.'],
                    ['title' => 'Stripping a chart to its argument', 'minutes' => 17, 'description' => 'Removing everything that is not doing work.', 'resource' => [
                        'title' => 'Chart reduction checklist',
                        'file' => 'chart-reduction-checklist.pdf',
                        'body' => "Apply in order. Stop when removing the next thing makes the chart harder to read.\n\nRemove the chart border. Remove the background fill. Remove vertical gridlines unless reading exact values matters.\n\nLighten horizontal gridlines to the point where you notice them only when looking for them.\n\nLabel series directly on the line or bar. Delete the legend.\n\nAxis: start at zero for bars, always. For lines, start where the data lives and say so in the subtitle.\n\nColour: one colour for everything, plus one accent for the series your claim is about. Two accents means you have two claims and need two charts.\n\nTitle: write the finding, not the subject. \"Churn doubled after the March price change\" — not \"Monthly churn\".",
                    ]],
                ],
            ],
            [
                'title' => 'Presenting it',
                'lessons' => [
                    ['title' => 'One chart, one sentence', 'minutes' => 15, 'description' => 'Writing the title as the finding, and what changes when you do.'],
                    ['title' => 'Handling the first objection', 'minutes' => 14, 'description' => 'Anticipating the question that always comes, and answering it in the chart.'],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: chart clinic', 'description' => 'Bring a chart you are unhappy with. We rebuild a handful of them live.', 'minutes' => 60],
        ],
        'faqs' => [
            ['Is this about a specific tool?', 'It works in Excel, Google Sheets and Numbers. Nothing depends on a feature only one of them has.'],
        ],
    ],

    [
        'slug' => 'client-acquisition-for-freelancers',
        'title' => 'Client Acquisition for Freelancers',
        'category' => 'business',
        'instructor' => 'daniel-okoro',
        'price' => 0,
        'level' => CourseLevel::Beginner,
        'short' => 'A free short course on finding better clients, quoting with confidence and stopping the feast-and-famine cycle.',
        'description' => "I have run an independent studio for eleven years. This short course is the part of that experience that is genuinely transferable: where good clients actually come from, how to quote without flinching, and the habit that ended feast-and-famine for me.\n\nIt is free because it is the course I wish someone had given me in year one, and because most of what is written about freelancing is written by people who stopped freelancing.",
        'outcomes' => [
            'Identify the two or three channels that will realistically bring you work',
            'Quote a project without discounting reflexively',
            'Write a proposal that closes without a follow-up call',
            'Build a pipeline habit that takes twenty minutes a week',
        ],
        'requirements' => ['You freelance, or intend to'],
        'audience' => [
            'Designers, writers and developers working independently',
            'Anyone about to leave employment to go independent',
        ],
        'modules' => [
            [
                'title' => 'Where work comes from',
                'lessons' => [
                    ['title' => 'The honest channel audit', 'minutes' => 14, 'preview' => true, 'description' => 'Where your last ten projects actually came from, and what that tells you to stop doing.'],
                    ['title' => 'Referrals as a system', 'minutes' => 19, 'preview' => true, 'description' => 'Making referrals repeatable rather than lucky.'],
                    ['title' => 'The twenty-minute pipeline habit', 'minutes' => 16, 'description' => 'A weekly routine that prevents the quiet months.'],
                ],
            ],
            [
                'title' => 'Quoting and closing',
                'lessons' => [
                    ['title' => 'Pricing from value, defended', 'minutes' => 24, 'description' => 'How to arrive at a number and how to hold it in the conversation that follows.'],
                    ['title' => 'The proposal that closes itself', 'minutes' => 21, 'description' => 'Structure, length, and the section most freelancers omit.', 'resource' => [
                        'title' => 'Proposal skeleton',
                        'file' => 'proposal-skeleton.pdf',
                        'body' => "Four pages maximum. Anything longer reads as uncertainty.\n\nPage 1 — What you told me. Their problem in their words, from the call. Nothing else. This page does more work than the other three combined.\n\nPage 2 — What I propose to do about it. Phases, deliverables, and what is explicitly out of scope.\n\nPage 3 — How it runs. Timeline, what you need from them and by when, revision rounds included, payment schedule.\n\nPage 4 — Price and next step. One number per option, maximum three options. A single sentence saying exactly what happens when they say yes.\n\nSend it as a PDF. Do not present it live unless they ask — a proposal that needs narration is not finished.",
                    ]],
                    ['title' => 'When to walk away', 'minutes' => 13, 'description' => 'The four signals that a project will cost more than it pays.'],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: proposal and pricing questions', 'description' => 'An open session. Bring a live quote you are unsure about.', 'minutes' => 60],
        ],
        'faqs' => [
            ['Is this really free?', 'Yes. Create an account and enrol; nothing is charged and you get the certificate on completion like any other course.'],
        ],
    ],

    [
        'slug' => 'advanced-media-buying',
        'title' => 'Advanced Media Buying',
        'category' => 'marketing',
        'instructor' => 'priya-raman',
        'price' => 19900,
        'level' => CourseLevel::Advanced,
        'status' => CourseStatus::Draft,
        'short' => 'Incrementality testing, media mix modelling and buying at scale. Currently in production.',
        'description' => "A follow-on to Performance Marketing Foundations, covering the work that only becomes relevant above roughly ₹50 lakh of annual spend: incrementality testing, geo experiments, and building a defensible media mix model.\n\nThis course is in production and not yet open for enrolment.",
        'outcomes' => [
            'Design and run a geo-based incrementality test',
            'Build a media mix model without a data science team',
            'Negotiate and structure direct buys',
        ],
        'requirements' => ['Performance Marketing Foundations, or equivalent experience'],
        'audience' => ['Marketers managing substantial budgets'],
        'modules' => [
            [
                'title' => 'Incrementality',
                'lessons' => [
                    ['title' => 'Why platform-reported conversions overstate', 'minutes' => 22, 'description' => 'Attribution, and the gap between reported and incremental.'],
                    ['title' => 'Designing a geo holdout', 'minutes' => 30, 'description' => 'Choosing regions, sizing the test and reading the result.'],
                ],
            ],
        ],
        'live_classes' => [],
        'faqs' => [],
    ],

    [
        'slug' => 'sql-for-data-engineering',
        'title' => 'SQL for Data Engineering',
        'category' => 'data-engineering',
        'instructor' => 'vikram-nair',
        'price' => 9900,
        'compare_at' => 13900,
        'level' => CourseLevel::Beginner,
        'featured' => true,
        'short' => 'From your first SELECT to the window functions and optimisation work a data engineering job actually asks for.',
        'description' => "Every data role starts with SQL, and most SQL teaching stops well short of what a job needs. This course does not.\n\nWe go from fundamentals through joins, subqueries and CTEs, then into the part most courses skip: window functions, reading an execution plan, and optimising a query that is slow for a reason you cannot see yet. The last module is entirely real-world problems — deduplicating messy data, sessionizing event logs — worked the way you would actually encounter them, not as isolated syntax drills.\n\nBy the end you will be able to open an unfamiliar schema and answer a real question with it, quickly and correctly.",
        'outcomes' => [
            'Write joins, subqueries and CTEs without reaching for documentation',
            'Use window functions for running totals, rankings and comparisons across rows',
            'Read an execution plan and know where a slow query is losing time',
            'Solve the kind of messy, ambiguous SQL problem an interview or a real job actually gives you',
        ],
        'requirements' => [
            'No prior SQL experience required',
            'A computer that can run a free database (setup is covered in Module 1)',
        ],
        'audience' => [
            'Anyone starting a data analyst or data engineer path from zero',
            'Developers who write SQL occasionally and want to stop guessing',
            'Analysts who know basic SELECT statements and want to go further',
        ],
        'modules' => [
            [
                'title' => 'SQL fundamentals',
                'description' => 'The core statements everything else in this course builds on.',
                'lessons' => [
                    ['title' => 'What this course covers, and how to practise it', 'minutes' => 8, 'preview' => true, 'description' => 'Setting up a free practice database and the dataset used throughout the course.'],
                    ['title' => 'SELECT, WHERE and ORDER BY in practice', 'minutes' => 19, 'preview' => true, 'description' => 'Filtering and sorting precisely, including the NULL-handling mistakes almost everyone makes once.'],
                    ['title' => 'Aggregations and GROUP BY', 'minutes' => 24, 'description' => 'Turning rows into answers: counts, sums, averages, and the HAVING clause people forget exists.'],
                    ['title' => 'Data types and working with NULL', 'minutes' => 16, 'description' => 'Why NULL breaks naive comparisons, and the functions that handle it correctly.'],
                ],
            ],
            [
                'title' => 'Joins, subqueries and CTEs',
                'description' => 'Combining data from more than one place without getting the row count wrong.',
                'lessons' => [
                    ['title' => 'Inner, outer and cross joins in practice', 'minutes' => 27, 'description' => 'Choosing the right join type, and the row-multiplication bug that catches almost everyone once.'],
                    ['title' => 'Subqueries: correlated vs uncorrelated', 'minutes' => 22, 'description' => 'When a subquery is the clearest tool, and when it quietly becomes a performance problem.'],
                    ['title' => 'Common table expressions for readable logic', 'minutes' => 20, 'description' => 'Breaking a complicated query into named, testable steps.', 'resource' => [
                        'title' => 'Joins and CTEs cheat sheet',
                        'file' => 'sql-joins-ctes-cheat-sheet.pdf',
                        'body' => 'A quick-reference for the four join types, when to reach for a CTE instead of a subquery, and a checklist for spotting an accidental row-multiplying join before it reaches production.',
                    ]],
                    ['title' => 'Recursive CTEs for hierarchical data', 'minutes' => 23, 'description' => 'Walking an org chart or a category tree without a loop in application code.'],
                ],
            ],
            [
                'title' => 'Window functions and optimisation',
                'description' => 'The techniques that separate someone who knows SQL from someone who can use it on a real warehouse.',
                'lessons' => [
                    ['title' => 'Window functions: ROW_NUMBER, RANK and LAG/LEAD', 'minutes' => 29, 'description' => 'Comparing a row to the rows around it, without a self-join.'],
                    ['title' => 'Running totals and moving averages', 'minutes' => 21, 'description' => 'Frame clauses, explained by building the report that needs them.'],
                    ['title' => 'Reading an execution plan', 'minutes' => 26, 'description' => 'What the database is actually doing, in the order it actually does it.'],
                    ['title' => 'Indexing and query optimisation basics', 'minutes' => 24, 'description' => 'The handful of indexing decisions that fix most slow queries.'],
                ],
            ],
            [
                'title' => 'Data analysis and real-world problems',
                'description' => 'Applying everything above to problems shaped like the ones a job actually gives you.',
                'lessons' => [
                    ['title' => 'Turning a business question into SQL', 'minutes' => 18, 'description' => 'The translation step most courses skip: going from "which customers are at risk" to a query.'],
                    ['title' => 'Real-world problem: deduplicating messy data', 'minutes' => 25, 'description' => 'Finding and removing duplicates when no single column uniquely identifies a row.', 'resource' => [
                        'title' => 'Messy customer records dataset',
                        'file' => 'messy-customer-records.pdf',
                        'body' => 'A worked dataset with near-duplicate customer records — same person, different spellings and formatting. Use the deduplication techniques from the lesson before checking the solution query in the next lesson.',
                    ]],
                    ['title' => 'Real-world problem: sessionizing event logs', 'minutes' => 28, 'description' => 'Grouping raw click events into sessions using nothing but window functions.'],
                    ['title' => 'Writing SQL a teammate can read in six months', 'type' => LessonType::Text, 'minutes' => 12, 'description' => 'Formatting, naming and CTE structure conventions that make a query maintainable.', 'content' => "The SQL that causes problems is rarely wrong. It is unreadable.\n\n**Name CTEs for what they contain, not how they were built.** `active_customers_last_90_days`, not `cte1`.\n\n**One join condition per line, aligned.** A reviewer should be able to scan the ON clauses without parsing the whole statement.\n\n**Push filtering as early as possible.** Filter in the CTE that reads the table, not three joins later — it is faster and it tells the reader what the query is actually about.\n\n**Comment the why, not the what.** `-- excludes test accounts, see ticket DATA-482` earns its place. `-- filter customers` does not.\n\nA query that takes you thirty extra seconds to format will save someone else thirty minutes six months from now — often you."],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: query optimisation clinic', 'description' => 'Submit a slow query in advance and we will read its execution plan together and fix it live.', 'preparation' => 'Have a query and, if possible, its execution plan ready to share.', 'minutes' => 75],
            ['title' => 'Live: SQL problem-solving session', 'description' => 'Working through unseen SQL problems together, the way a take-home interview test would present them.', 'past' => true, 'minutes' => 60],
        ],
        'faqs' => [
            ['Which SQL dialect does this use?', 'Standard ANSI SQL throughout, with notes wherever Postgres, MySQL and Snowflake syntax genuinely differ.'],
            ['Do I need a database installed?', 'A free, browser-based practice database is set up in the first lesson. No local installation is required.'],
            ['Is this enough to pass a SQL interview?', 'It covers the level almost every data analyst and data engineer SQL interview tests, including the "messy real-world problem" style questions in the final module.'],
        ],
    ],

    [
        'slug' => 'python-for-data-engineering',
        'title' => 'Python for Data Engineering',
        'category' => 'data-engineering',
        'instructor' => 'vikram-nair',
        'price' => 10400,
        'compare_at' => 13900,
        'level' => CourseLevel::Beginner,
        'short' => 'The Python you need to process data, call APIs, and write ETL scripts that keep working when nobody is watching them.',
        'description' => "This is not a general Python course. Every example here is a data engineering task: reading a messy file, calling a rate-limited API, cleaning a DataFrame, writing a script that can safely run twice.\n\nWe move from fundamentals through pandas and into the part that actually matters on the job — automation, logging, and idempotency, so a script you write and forget about does not quietly corrupt data the second time it runs.\n\nYou will finish having built a real script that pulls from an API and lands the result in a database table, structured the way a production ETL job should be.",
        'outcomes' => [
            'Process and clean real-world data with pandas confidently',
            'Call a REST API correctly, including pagination and authentication',
            'Write an ETL script that is safe to re-run without duplicating or corrupting data',
            'Structure Python data code so someone else on the team can maintain it',
        ],
        'requirements' => [
            'No prior programming experience required',
            'A computer able to install Python (setup is covered in Module 1)',
        ],
        'audience' => [
            'SQL users who want to add scripting and automation to their toolkit',
            'Analysts moving toward a data engineering role',
            'Anyone automating a manual, repetitive data task for the first time',
        ],
        'modules' => [
            [
                'title' => 'Python fundamentals for data work',
                'description' => 'Just enough of the language to read and write real data scripts, taught through data examples.',
                'lessons' => [
                    ['title' => 'Setting up a real working environment', 'minutes' => 14, 'preview' => true, 'description' => 'A proper virtual environment and editor setup, not just a notebook you will outgrow.'],
                    ['title' => 'Variables, control flow and functions', 'minutes' => 26, 'description' => 'The parts of the language you will actually use every day, and nothing you will not.'],
                    ['title' => 'Working with files: CSV, JSON and plain text', 'minutes' => 21, 'preview' => true, 'description' => 'Reading and writing the file formats data actually arrives in.'],
                    ['title' => 'Error handling that fails loudly', 'minutes' => 17, 'description' => 'Catching the exceptions worth catching, and letting the rest surface instead of hiding silently.'],
                ],
            ],
            [
                'title' => 'Processing data with pandas',
                'description' => 'The library you will use in almost every data engineering script you write.',
                'lessons' => [
                    ['title' => 'DataFrames: loading, filtering and reshaping', 'minutes' => 28, 'description' => 'The operations that cover most day-to-day pandas work.'],
                    ['title' => 'Cleaning messy real-world data', 'minutes' => 25, 'description' => 'Missing values, inconsistent formatting and type coercion, worked on a genuinely messy dataset.', 'resource' => [
                        'title' => 'Messy sales export dataset',
                        'file' => 'messy-sales-export.pdf',
                        'body' => 'A description of the sample dataset used in this lesson and the follow-up exercise: inconsistent date formats, missing region codes, and duplicate rows to clean with the techniques just covered.',
                    ]],
                    ['title' => 'Merging and aggregating like a warehouse join', 'minutes' => 22, 'description' => 'Combining and summarising DataFrames the way you would join and aggregate in SQL.'],
                    ['title' => 'Why pandas is not always the right tool', 'type' => LessonType::Text, 'minutes' => 10, 'description' => 'When to push work back into the database instead of pulling it into memory.', 'content' => "Pandas loads data into memory on one machine. That is fine until it is not.\n\nPush the work back to the database when: the data does not fit comfortably in memory, the transformation is a straightforward filter or aggregation the database already does well, or the result only needs to be computed once and reused.\n\nKeep it in pandas when: the logic is genuinely easier to express in Python than SQL, you are joining data from two different systems that cannot be joined in one query, or you are prototyping before deciding where the logic should permanently live.\n\nThe failure mode to watch for: a script that pulls an entire table into a DataFrame to do something a single SQL query could have done in the warehouse, in a fraction of the time."],
                ],
            ],
            [
                'title' => 'APIs and automation',
                'description' => 'Getting data out of systems that were not built to be queried with SQL.',
                'lessons' => [
                    ['title' => 'Calling a REST API and handling pagination', 'minutes' => 24, 'description' => 'Fetching a complete dataset from an API that only returns one page at a time.'],
                    ['title' => 'Authentication: keys, tokens and secrets', 'minutes' => 18, 'description' => 'Handling credentials without hardcoding them into a script that ends up in version control.'],
                    ['title' => 'Scheduling a script without a full orchestrator', 'minutes' => 16, 'description' => 'Running a script reliably with cron before you need something heavier.'],
                    ['title' => 'Logging so you can debug a 3am failure', 'minutes' => 19, 'description' => 'What to log, at what level, so a failure is diagnosable from the log alone.'],
                ],
            ],
            [
                'title' => 'ETL scripting and pipelines',
                'description' => 'Putting it together into something you would actually trust in production.',
                'lessons' => [
                    ['title' => 'Structuring an ETL script that is testable', 'minutes' => 23, 'description' => 'Separating extract, transform and load into functions you can test independently.'],
                    ['title' => 'Writing to a database from Python safely', 'minutes' => 20, 'description' => 'Parameterised queries, batching inserts, and avoiding the mistakes that corrupt a table.'],
                    ['title' => 'Idempotency: making a script safe to re-run', 'minutes' => 22, 'description' => 'Designing a load so running it twice does not duplicate or double-count anything.'],
                    ['title' => 'Real-world project: syncing an API into a warehouse table', 'minutes' => 30, 'description' => 'Building the complete script, end to end, using everything from this course.', 'resource' => [
                        'title' => 'API-to-warehouse project brief',
                        'file' => 'api-to-warehouse-project-brief.pdf',
                        'body' => 'The full project brief: a public API, the target table schema, and the acceptance criteria your script should meet — including what should happen if it is run twice in a row.',
                    ]],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: code review clinic', 'description' => 'Submit a script and get a live code review focused on structure, error handling and idempotency.', 'preparation' => 'Have a script ready to share, even an unfinished one.', 'minutes' => 75],
            ['title' => 'Live: debugging a broken ETL script', 'description' => 'Diagnosing a deliberately broken pipeline together, using the logging and error-handling techniques from the course.', 'past' => true, 'minutes' => 60],
        ],
        'faqs' => [
            ['Do I need prior programming experience?', 'No. Module 1 starts from a proper setup and the basics of the language, taught through data examples throughout.'],
            ['Which Python version does this use?', 'Python 3.11 or newer. Setup instructions for Windows, macOS and Linux are included.'],
            ['Is this enough Python to get a data engineering job?', 'Combined with the SQL course, this covers the practical scripting ability most data engineering job descriptions actually test for.'],
        ],
    ],

    [
        'slug' => 'azure-data-factory',
        'title' => 'Azure Data Factory',
        'category' => 'data-engineering',
        'instructor' => 'vikram-nair',
        'price' => 11400,
        'compare_at' => 14900,
        'level' => CourseLevel::Intermediate,
        'short' => 'Build, schedule and monitor real pipelines in Azure Data Factory — including the error handling most tutorials skip.',
        'description' => "Most ADF tutorials show you how to build a pipeline that works once, on a demo dataset, on a good day. This course is about the pipeline that still works after the source schema changes, a run fails halfway through, and nobody is watching the monitoring page when it happens.\n\nWe cover the building blocks — linked services, datasets, the Copy activity and data flows — then spend real time on parameters, triggers and orchestration, and finish with monitoring and error handling, the module that separates a working demo from something you can put in production.",
        'outcomes' => [
            'Build parameterised, reusable pipelines rather than one-off copies',
            'Choose correctly between the Copy activity and a mapping data flow',
            'Orchestrate multiple pipelines with triggers and control-flow activities',
            'Read the monitoring view and design pipelines that fail safely, not silently',
        ],
        'requirements' => [
            'A free-tier Azure subscription (setup covered in Module 1)',
            'Basic SQL, useful but not required',
        ],
        'audience' => [
            'Data engineers working in, or moving into, an Azure environment',
            'ETL developers migrating from an on-premises tool to a cloud-native one',
            'Analysts who need to understand pipelines their team already runs',
        ],
        'modules' => [
            [
                'title' => 'ADF fundamentals',
                'description' => 'The building blocks every pipeline in this course is made from.',
                'lessons' => [
                    ['title' => 'What ADF is for, and what it is not', 'minutes' => 13, 'preview' => true, 'description' => 'Where ADF fits next to Synapse, Databricks and a plain scheduled script.'],
                    ['title' => 'Linked services and connecting to real sources', 'minutes' => 22, 'preview' => true, 'description' => 'Connecting to a database, a storage account and an API-based source.'],
                    ['title' => 'Datasets: describing the shape of your data', 'minutes' => 16, 'description' => 'The difference between a linked service, a dataset and an activity, made concrete.'],
                    ['title' => 'Your first pipeline: a simple copy', 'minutes' => 20, 'description' => 'Building, running and checking the result of a complete pipeline end to end.'],
                ],
            ],
            [
                'title' => 'Moving and transforming data',
                'description' => 'The two main ways data actually moves and changes shape inside ADF.',
                'lessons' => [
                    ['title' => 'The Copy activity in depth', 'minutes' => 24, 'description' => 'Settings that matter in production: fault tolerance, staging, and performance tuning.'],
                    ['title' => 'Mapping data flows for transformation without code', 'minutes' => 29, 'description' => 'Joins, aggregations and derived columns using the visual data flow designer.'],
                    ['title' => 'Handling schema drift gracefully', 'minutes' => 19, 'description' => 'Designing pipelines that survive a source adding or renaming a column.'],
                    ['title' => 'Data flow vs stored-procedure activity', 'type' => LessonType::Text, 'minutes' => 11, 'description' => 'A decision guide for where transformation logic should actually live.', 'content' => "The honest answer is \"it depends,\" but the decision is not random.\n\n**Use a mapping data flow when:** the transformation is genuinely visual — several joins, aggregations and derived columns — and the team maintaining it is comfortable in ADF rather than SQL.\n\n**Use a stored procedure (or push the logic into the warehouse) when:** the warehouse already has the compute to do it efficiently, the transformation is a small number of well-understood SQL operations, or the team maintaining it is more comfortable in SQL than in ADF's visual designer.\n\n**Avoid:** duplicating the same transformation logic in both places. Pick one, and treat the other as orchestration only."],
                ],
            ],
            [
                'title' => 'Parameters, triggers and orchestration',
                'description' => 'Turning one-off pipelines into a reusable, scheduled system.',
                'lessons' => [
                    ['title' => 'Parameterising pipelines for reuse', 'minutes' => 23, 'description' => 'One pipeline definition, many sources — instead of copy-pasting a pipeline per table.'],
                    ['title' => 'Schedule, tumbling window and event-based triggers', 'minutes' => 21, 'description' => 'Choosing the right trigger type for the way your source actually produces data.'],
                    ['title' => 'Chaining pipelines and passing values between them', 'minutes' => 18, 'description' => 'Building a multi-stage pipeline out of smaller, independently testable ones.'],
                    ['title' => 'Control flow: ForEach, If and Until activities', 'minutes' => 20, 'description' => 'The activities that let a pipeline make decisions instead of just running in a straight line.'],
                ],
            ],
            [
                'title' => 'Monitoring and error handling',
                'description' => 'What makes a pipeline production-grade rather than a working demo.',
                'lessons' => [
                    ['title' => 'Reading the monitoring view like an incident report', 'minutes' => 17, 'description' => 'Diagnosing exactly where and why a pipeline run failed.'],
                    ['title' => 'Retry policies and failure paths', 'minutes' => 19, 'description' => 'Deciding what should retry automatically and what should stop and alert a human.'],
                    ['title' => 'Alerting when a pipeline fails silently', 'minutes' => 15, 'description' => 'Setting up alerts so a failure reaches someone before a stakeholder notices stale data.'],
                    ['title' => 'Real-world project: an incremental API-to-warehouse pipeline', 'minutes' => 27, 'description' => 'Building a complete, parameterised, monitored pipeline using everything in this course.', 'resource' => [
                        'title' => 'Incremental pipeline project brief',
                        'file' => 'adf-incremental-pipeline-brief.pdf',
                        'body' => 'The project brief: an API source, a target warehouse table, and the requirement to load only new or changed records on each run — with acceptance criteria for failure handling and alerting.',
                    ]],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: pipeline design review', 'description' => 'Submit a pipeline design and we will pressure-test its parameterisation and failure handling together.', 'preparation' => 'Have a pipeline diagram or a working ADF pipeline ready to share.', 'minutes' => 75],
            ['title' => 'Live: debugging a failed production run', 'description' => 'Diagnosing a deliberately broken pipeline using the monitoring view, live.', 'past' => true, 'minutes' => 60],
        ],
        'faqs' => [
            ['Do I need an Azure subscription?', 'Yes, a free-tier subscription is enough for every exercise in this course. Setup is covered in Module 1.'],
            ['Is this relevant if my warehouse is Snowflake, not Azure-native?', 'Yes. ADF is commonly used to orchestrate loads into Snowflake and other non-Azure warehouses, and that pattern is covered directly.'],
            ['Does this cover Azure Synapse too?', 'Synapse pipelines share the same engine as ADF, so the concepts transfer directly, though the course itself is built in ADF.'],
        ],
    ],

    [
        'slug' => 'snowflake-masterclass',
        'title' => 'Snowflake Masterclass',
        'category' => 'data-engineering',
        'instructor' => 'vikram-nair',
        'price' => 11900,
        'compare_at' => 15900,
        'level' => CourseLevel::Intermediate,
        'short' => 'Architecture, loading, sharing and performance — everything you need to run a real Snowflake warehouse, not just query one.',
        'description' => "Most Snowflake content teaches you to write a SELECT statement against a warehouse someone else built and tuned. This course teaches you to be the person who builds and tunes it.\n\nWe start with why Snowflake's architecture — storage separated from compute — changes how you design everything else, then move through loading data, streams and tasks for automation, secure data sharing, and finish with the performance and cost questions that come up in every real deployment: warehouse sizing, clustering, and why the bill looked like that this month.",
        'outcomes' => [
            'Explain Snowflake\'s architecture well enough to make real design decisions with it',
            'Load data reliably using COPY INTO, stages and Snowpipe',
            'Automate transformation with Streams and Tasks',
            'Diagnose and fix a warehouse that is slow or unexpectedly expensive',
        ],
        'requirements' => [
            'Comfort with SQL (the SQL for Data Engineering course, or equivalent)',
            'A Snowflake trial account (free to create, covered in Module 1)',
        ],
        'audience' => [
            'Data engineers who need to own a Snowflake environment, not just query it',
            'Analysts moving into a warehouse-ownership role',
            'Teams migrating from another warehouse onto Snowflake',
        ],
        'modules' => [
            [
                'title' => 'Snowflake architecture and setup',
                'description' => 'The design decision that makes everything else in Snowflake make sense.',
                'lessons' => [
                    ['title' => 'Why Snowflake separates storage from compute', 'minutes' => 18, 'preview' => true, 'description' => 'The architectural decision behind almost every Snowflake behaviour you will meet later.'],
                    ['title' => 'Databases, schemas and the account hierarchy', 'minutes' => 15, 'description' => 'Organising an account so permissions and billing stay manageable as it grows.'],
                    ['title' => 'Setting up your first virtual warehouse', 'minutes' => 17, 'preview' => true, 'description' => 'Sizing, auto-suspend and auto-resume, and what each setting actually costs you.'],
                    ['title' => 'Roles and access control basics', 'minutes' => 20, 'description' => 'Snowflake\'s role hierarchy, and a sane starting structure for a real team.'],
                ],
            ],
            [
                'title' => 'Getting data in',
                'description' => 'Every pattern for loading data, from a one-off file to continuous streaming.',
                'lessons' => [
                    ['title' => 'Loading data with COPY INTO', 'minutes' => 24, 'description' => 'The workhorse loading command, and the file-format options that prevent load failures.'],
                    ['title' => 'Stages: internal vs external', 'minutes' => 19, 'description' => 'Choosing where files live before they are loaded, and why it matters for cost and security.'],
                    ['title' => 'Semi-structured data: querying JSON natively', 'minutes' => 23, 'description' => 'The VARIANT type and dot-notation querying, without flattening everything first.', 'resource' => [
                        'title' => 'Semi-structured data reference',
                        'file' => 'snowflake-json-querying-reference.pdf',
                        'body' => 'A reference sheet for querying nested JSON and arrays directly in Snowflake using dot notation, LATERAL FLATTEN, and the common patterns for extracting a clean table from messy nested data.',
                    ]],
                    ['title' => 'Continuous loading with Snowpipe', 'minutes' => 21, 'description' => 'Loading files automatically as they land, without a scheduled batch job.'],
                ],
            ],
            [
                'title' => 'Streams, tasks and sharing',
                'description' => 'Automating what happens to data after it lands, and sharing it without copying it.',
                'lessons' => [
                    ['title' => 'Change data capture with Streams', 'minutes' => 22, 'description' => 'Tracking exactly what changed in a table since it was last processed.'],
                    ['title' => 'Automating pipelines with Tasks', 'minutes' => 20, 'description' => 'Scheduling SQL to run on its own, and chaining tasks into a dependency graph.'],
                    ['title' => 'Secure data sharing without copying data', 'minutes' => 18, 'description' => 'Sharing live data with another team or company without an export-and-reload cycle.'],
                    ['title' => 'When to share data vs export it', 'type' => LessonType::Text, 'minutes' => 9, 'description' => 'A short decision guide for a question that comes up in almost every data-sharing conversation.', 'content' => "Secure data sharing is not always the right answer, even inside Snowflake.\n\n**Share when:** the recipient also uses Snowflake, the data changes regularly and staleness would be a problem, or you want to avoid ever having a second, drifting copy of the truth.\n\n**Export when:** the recipient uses a different platform entirely, the data is a one-off extract rather than an ongoing feed, or contractual or compliance requirements specifically require a delivered file rather than live access.\n\nThe mistake to avoid: building a scheduled export pipeline to solve a problem that a data share would have solved with no pipeline at all."],
                ],
            ],
            [
                'title' => 'Performance and real-world projects',
                'description' => 'The questions that come up once a warehouse is real and someone is paying the bill.',
                'lessons' => [
                    ['title' => 'Warehouse sizing and auto-suspend economics', 'minutes' => 20, 'description' => 'Why the wrong warehouse size costs more in both directions, and how to find the right one.'],
                    ['title' => 'Query profiling and optimisation', 'minutes' => 25, 'description' => 'Reading the query profile to find out where time and credits are actually going.'],
                    ['title' => 'Clustering keys for large tables', 'minutes' => 19, 'description' => 'When clustering helps, when it does not, and how to tell the difference.'],
                    ['title' => 'Real-world project: a sales reporting warehouse', 'minutes' => 28, 'description' => 'Designing and loading a warehouse for a reporting use case from scratch.', 'resource' => [
                        'title' => 'Sales warehouse project brief',
                        'file' => 'snowflake-sales-warehouse-brief.pdf',
                        'body' => 'The full project brief: source files, target schema, and the reporting queries your warehouse design needs to answer efficiently.',
                    ]],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: warehouse cost review', 'description' => 'Bring a warehouse (yours or the case study) and we will find out where the credits are actually going.', 'preparation' => 'Have query history or a case-study account ready to review.', 'minutes' => 75],
            ['title' => 'Live: designing a schema for a new project', 'description' => 'Working through a schema design together, from a real set of requirements.', 'past' => true, 'minutes' => 60],
        ],
        'faqs' => [
            ['Do I need my own Snowflake account?', 'A free trial account, created in Module 1, is enough for every exercise in this course.'],
            ['Is this only relevant on a specific cloud provider?', 'No. Everything here applies whether the underlying Snowflake account runs on AWS, Azure or GCP.'],
            ['How much does the practice environment cost to run?', 'Comfortably within Snowflake\'s free trial credits if you follow the auto-suspend guidance in Module 1.'],
        ],
    ],

    [
        'slug' => 'dbt-for-modern-data-engineering',
        'title' => 'dbt for Modern Data Engineering',
        'category' => 'data-engineering',
        'instructor' => 'ananya-krishnan',
        'price' => 10900,
        'compare_at' => 14900,
        'level' => CourseLevel::Intermediate,
        'short' => 'Models, tests, documentation and incremental loads — the dbt workflow that turns a warehouse into something a team can trust.',
        'description' => "dbt did not invent SQL transformation. It made it possible to test it, document it, and review it the way software has been reviewed for years — and that discipline is the actual point of this course.\n\nWe cover models, sources and tests, then macros and incremental models for performance, and finish with the production workflow: environments, scheduled runs, and what a good dbt pull request actually looks like. The final project models a subscription business's revenue end to end.",
        'outcomes' => [
            'Structure a dbt project that stays organised past the first ten models',
            'Write tests that catch real data problems before a stakeholder does',
            'Choose the right materialization and build performant incremental models',
            'Run dbt as part of a real, scheduled production workflow',
        ],
        'requirements' => [
            'Working SQL knowledge (the SQL for Data Engineering course, or equivalent)',
            'Access to a warehouse — a free-tier Snowflake or BigQuery account works',
        ],
        'audience' => [
            'Data analysts moving into analytics engineering',
            'Data engineers adding a disciplined transformation layer to existing pipelines',
            'Teams currently transforming data with untested, undocumented stored procedures',
        ],
        'modules' => [
            [
                'title' => 'dbt fundamentals',
                'description' => 'What dbt actually does, and the project structure everything else depends on.',
                'lessons' => [
                    ['title' => 'What dbt actually does, and does not do', 'minutes' => 14, 'preview' => true, 'description' => 'Placing dbt correctly next to your warehouse, your orchestrator and your BI tool.'],
                    ['title' => 'Setting up a project and connecting a warehouse', 'minutes' => 19, 'preview' => true, 'description' => 'A working dbt project connected to a real warehouse, ready for the first model.'],
                    ['title' => 'Models: your first SELECT-as-a-table', 'minutes' => 22, 'description' => 'How a model becomes a table or view, and the folder conventions that keep it findable.'],
                    ['title' => 'Project structure that scales past ten models', 'minutes' => 18, 'description' => 'Staging, intermediate and mart layers — and why the boundary between them matters.'],
                ],
            ],
            [
                'title' => 'Sources, tests and documentation',
                'description' => 'The discipline that turns a pile of SQL files into something a team can trust.',
                'lessons' => [
                    ['title' => 'Declaring sources and freshness checks', 'minutes' => 17, 'description' => 'Telling dbt where raw data comes from, and getting alerted when it stops arriving.'],
                    ['title' => 'Writing tests that catch real problems', 'minutes' => 24, 'description' => 'Not-null and unique tests are the start, not the destination — writing tests for business logic that actually matters.'],
                    ['title' => 'Generating documentation your team will read', 'minutes' => 16, 'description' => 'Docs and column descriptions that live next to the code, so they stay accurate.', 'resource' => [
                        'title' => 'dbt project structure reference',
                        'file' => 'dbt-project-structure-reference.pdf',
                        'body' => 'A reference folder structure for staging, intermediate and mart models, naming conventions for each layer, and a checklist for what belongs in a schema.yml file.',
                    ]],
                    ['title' => 'A model naming convention that survives a team of five', 'type' => LessonType::Text, 'minutes' => 10, 'description' => 'The naming pattern used throughout this course, and why each part of it exists.', 'content' => "A model naming convention only matters once more than one person touches the project. By then it is expensive to change, so start with one.\n\n**Pattern: `<layer>_<subject>__<detail>`** — for example `stg_stripe__invoices`, `int_orders__joined_with_refunds`, `fct_revenue__monthly`.\n\n**The layer prefix is not optional.** `stg_`, `int_` and `fct_`/`dim_` tell a reader what a model is for before they open it.\n\n**The double underscore separates subject from detail deliberately.** It makes the subject searchable and the detail scannable, which matters once a project has hundreds of models.\n\n**Never abbreviate the source system name.** `stripe`, not `strp`. You will forget what the abbreviation meant; you will not forget what `stripe` means."],
                ],
            ],
            [
                'title' => 'Macros and incremental models',
                'description' => 'Reducing repetition, and building models that scale to real data volumes.',
                'lessons' => [
                    ['title' => 'Jinja and macros: don\'t repeat yourself', 'minutes' => 21, 'description' => 'Turning a repeated pattern across models into one reusable macro.'],
                    ['title' => 'Incremental models: only processing new data', 'minutes' => 26, 'description' => 'Building a model that processes yesterday\'s new rows, not the whole table, every run.'],
                    ['title' => 'Choosing a materialization', 'minutes' => 19, 'description' => 'View, table, incremental or ephemeral — matching the materialization to how a model is actually used.'],
                    ['title' => 'Snapshots for slowly changing dimensions', 'minutes' => 20, 'description' => 'Tracking how a row changed over time, not just what it looks like now.'],
                ],
            ],
            [
                'title' => 'Production workflows',
                'description' => 'Running dbt as part of a real system, not just on a laptop.',
                'lessons' => [
                    ['title' => 'Environments: dev, staging and production', 'minutes' => 17, 'description' => 'Keeping development work from ever touching production tables.'],
                    ['title' => 'Orchestrating dbt runs on a schedule', 'minutes' => 18, 'description' => 'Running dbt reliably as part of a larger pipeline, not just manually.'],
                    ['title' => 'Reviewing a dbt pull request', 'minutes' => 16, 'description' => 'What an experienced analytics engineer actually looks for before approving a change.'],
                    ['title' => 'Real-world project: modelling a subscription business\'s revenue', 'minutes' => 30, 'description' => 'Building staging, intermediate and mart models for MRR and churn, tested and documented.', 'resource' => [
                        'title' => 'Subscription revenue project brief',
                        'file' => 'dbt-subscription-revenue-brief.pdf',
                        'body' => 'The full project brief: raw subscription and payment tables, the MRR and churn definitions your models need to implement, and the tests your final models must pass.',
                    ]],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: model review clinic', 'description' => 'Submit a model or a pull request and get a live review the way a senior analytics engineer would give one.', 'preparation' => 'Have a dbt model or project ready to share, even a small one.', 'minutes' => 75],
            ['title' => 'Live: debugging a failing test', 'description' => 'Diagnosing why a dbt test is failing and deciding whether the test or the data is wrong.', 'past' => true, 'minutes' => 60],
        ],
        'faqs' => [
            ['Which warehouse does this use?', 'Examples use Snowflake, but every concept applies directly to BigQuery, Redshift or Postgres with dbt.'],
            ['Do I need to know Jinja already?', 'No. Jinja is introduced from scratch in Module 3, at the point it is actually needed.'],
            ['Is dbt Cloud required, or does dbt Core work?', 'Everything is taught using dbt Core, which is free and works identically for learning purposes.'],
        ],
    ],

    [
        'slug' => 'power-bi-for-data-professionals',
        'title' => 'Power BI for Data Professionals',
        'category' => 'data-engineering',
        'instructor' => 'ananya-krishnan',
        'price' => 10900,
        'compare_at' => 14400,
        'level' => CourseLevel::Intermediate,
        'short' => 'Data modeling, Power Query, DAX and dashboard performance — built for people who already work with data, not first-time spreadsheet users.',
        'description' => "This course assumes you already understand data. It is about turning that understanding into dashboards other people trust and can actually use.\n\nWe start with data modelling — the single biggest factor in whether a Power BI report is fast and correct — then Power Query for shaping data before it hits the model, then DAX, taught around the one concept, filter context, that unlocks everything else in the language. The final module is dashboard design, performance and row-level security, finishing with a complete executive sales dashboard.",
        'outcomes' => [
            'Build a star-schema model that is fast and does not produce wrong numbers',
            'Shape and combine data from multiple sources in Power Query before it reaches the model',
            'Write DAX measures confidently, understanding filter context rather than guessing at syntax',
            'Design and ship a dashboard someone will actually read and trust',
        ],
        'requirements' => [
            'Comfort with spreadsheets and basic data concepts',
            'Power BI Desktop installed (free; setup covered in Module 1)',
        ],
        'audience' => [
            'Data analysts adding Power BI to an existing data skill set',
            'Data engineers who need to understand the BI layer their pipelines feed',
            'Excel power users moving to a proper BI tool',
        ],
        'modules' => [
            [
                'title' => 'Data modeling',
                'description' => 'The decision that determines whether everything downstream is fast and correct.',
                'lessons' => [
                    ['title' => 'Star schemas: why they still matter in Power BI', 'minutes' => 19, 'preview' => true, 'description' => 'Why a well-shaped model outperforms a clever DAX workaround every time.'],
                    ['title' => 'Relationships: cardinality and cross-filter direction', 'minutes' => 23, 'description' => 'The setting responsible for most "why is my number wrong" problems.'],
                    ['title' => 'Modeling for performance, not just for looks', 'minutes' => 20, 'preview' => true, 'description' => 'Column choices and table shapes that keep a large model fast.'],
                    ['title' => 'A modelling checklist before you build a single visual', 'type' => LessonType::Text, 'minutes' => 9, 'description' => 'What to verify about a model before spending time on the report layer.', 'content' => "Building visuals on top of a bad model wastes the visuals. Check the model first.\n\n**Is it a star schema?** One fact table, surrounded by dimension tables, not a single flat wide table.\n\n**Are relationships single-direction unless you have a specific reason?** Bi-directional filtering is a common source of circular, wrong results.\n\n**Are dates handled with a real date table?** Not the dates buried inside the fact table — a dedicated, marked date table, always.\n\n**Are high-cardinality columns (like free-text notes) excluded from the model?** They bloat size and rarely belong in a report.\n\nFive minutes on this checklist saves hours of debugging a DAX measure that was never the actual problem."],
                ],
            ],
            [
                'title' => 'Power Query',
                'description' => 'Shaping data correctly before it ever reaches the model.',
                'lessons' => [
                    ['title' => 'Shaping data before it hits the model', 'minutes' => 21, 'description' => 'Why transformation belongs in Power Query, not in DAX after the fact.'],
                    ['title' => 'Common transformations you will use every week', 'minutes' => 24, 'description' => 'Unpivoting, splitting columns, and the handful of steps that cover most real reports.'],
                    ['title' => 'Parameters and query folding', 'minutes' => 18, 'description' => 'Keeping transformations fast by letting the source system do the work where possible.'],
                    ['title' => 'Combining and appending queries from multiple sources', 'minutes' => 20, 'description' => 'Building one clean table out of several messy ones.'],
                ],
            ],
            [
                'title' => 'DAX',
                'description' => 'Built around the one idea that makes the rest of the language click.',
                'lessons' => [
                    ['title' => 'Calculated columns vs measures', 'minutes' => 22, 'description' => 'When each one is the right tool, and the performance cost of getting it backwards.'],
                    ['title' => 'CALCULATE and filter context', 'minutes' => 29, 'description' => 'The single concept that, once it clicks, makes the rest of DAX make sense.', 'resource' => [
                        'title' => 'DAX filter context worked examples',
                        'file' => 'dax-filter-context-examples.pdf',
                        'body' => 'A set of worked CALCULATE examples showing exactly how filter context changes at each step — the resource to return to whenever a DAX measure is not behaving the way you expect.',
                    ]],
                    ['title' => 'Time intelligence: YTD, prior period and moving averages', 'minutes' => 24, 'description' => 'The date-based calculations every business dashboard eventually needs.'],
                    ['title' => 'Common DAX patterns for business metrics', 'minutes' => 21, 'description' => 'Ranking, running totals and percent-of-total, built from patterns you can reuse.'],
                ],
            ],
            [
                'title' => 'Dashboards, reporting and performance',
                'description' => 'Turning a correct model and good measures into something people actually use.',
                'lessons' => [
                    ['title' => 'Designing a dashboard someone will actually read', 'minutes' => 20, 'description' => 'Layout, colour and hierarchy decisions that make a report scannable in ten seconds.'],
                    ['title' => 'Report performance: what slows a dashboard down', 'minutes' => 19, 'description' => 'Diagnosing whether a slow report is a model problem, a DAX problem, or a visual problem.'],
                    ['title' => 'Row-level security for shared reports', 'minutes' => 18, 'description' => 'Making sure each viewer sees only the rows they are supposed to, from one shared report.'],
                    ['title' => 'Real-world project: an executive sales dashboard', 'minutes' => 29, 'description' => 'Building a complete dashboard end to end, from raw data to a published, secured report.', 'resource' => [
                        'title' => 'Executive dashboard project brief',
                        'file' => 'power-bi-executive-dashboard-brief.pdf',
                        'body' => 'The full project brief: source tables, the metrics an executive sponsor asked for, and the acceptance criteria for the finished dashboard, including performance and row-level security requirements.',
                    ]],
                ],
            ],
        ],
        'live_classes' => [
            ['title' => 'Live: dashboard critique session', 'description' => 'Submit a report and get direct, practical feedback on its model, DAX and layout.', 'preparation' => 'Have a Power BI report ready to share, even a work in progress.', 'minutes' => 75],
            ['title' => 'Live: fixing a slow report', 'description' => 'Diagnosing and fixing a deliberately slow report together, live.', 'past' => true, 'minutes' => 60],
        ],
        'faqs' => [
            ['Do I need Power BI Desktop, or does the free tier work?', 'Power BI Desktop is free and is all you need for every exercise in this course.'],
            ['Is DAX covered from scratch?', 'Yes, though it moves quickly — this course assumes comfort with data concepts even for viewers new to DAX itself.'],
            ['Does this cover Power BI Service and publishing?', 'Yes, publishing and row-level security in the Power BI Service are covered in the final module.'],
        ],
    ],

];
