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

];
