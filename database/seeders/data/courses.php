<?php

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Enums\LessonType;

/**
 * Demonstration catalogue for Edunova.
 *
 * Replace this file's content with the academy's real programmes; the seeder
 * reads it verbatim, so no code has to change when the curriculum does.
 */
return [

    [
        'slug' => 'sql-for-data-engineering',
        'title' => 'Get Job-Ready with SQL Server in 30 Days',
        'category' => 'data-engineering',
        'instructor' => 'sharuk-shaik',
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
        'title' => 'Python for Data Engineers: Automate, Extract, Deliver',
        'category' => 'data-engineering',
        'instructor' => 'sharuk-shaik',
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
        'title' => 'Build Real Pipelines: Azure Data Factory in Action',
        'category' => 'data-engineering',
        'instructor' => 'sharuk-shaik',
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
        'title' => 'Get Job-Ready with Snowflake in 45 Days',
        'category' => 'data-engineering',
        'instructor' => 'sharuk-shaik',
        'price' => 11900,
        'compare_at' => 15900,
        'level' => CourseLevel::Intermediate,
        'featured' => true,
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
        'title' => 'dbt in Production: Transform Data Like a Pro Team',
        'category' => 'data-engineering',
        'instructor' => 'sharuk-shaik',
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
        'title' => 'Live Project: Power BI Dashboards on Snowflake Data',
        'category' => 'data-engineering',
        'instructor' => 'sharuk-shaik',
        'price' => 10900,
        'compare_at' => 14400,
        'level' => CourseLevel::Intermediate,
        'featured' => true,
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

    // Arun Singh has no active programme of his own yet. These two records
    // are historical/portfolio placeholders only — status Archived (shown
    // to admins as "Courses Sold") keeps them out of the public catalogue
    // and out of Course::published() everywhere it is used.
    [
        'slug' => 'fullstack-web-development-bootcamp',
        'title' => 'Full-Stack Web Development Bootcamp',
        'category' => 'data-engineering',
        'instructor' => 'arun-singh',
        'price' => 13900,
        'compare_at' => 17900,
        'level' => CourseLevel::Beginner,
        'status' => CourseStatus::Archived,
        'short' => 'Frontend, backend and the database in between — a complete web application, built and deployed end to end.',
        'description' => "A previously run cohort-based programme covering the full stack: a modern frontend framework, a backend API and its database, and deployment to a real server.\n\nKept here for reference — this programme is not currently open for enrolment.",
        'outcomes' => [
            'Build a complete web application, frontend to database',
            'Deploy an application to a Linux server correctly',
        ],
        'requirements' => ['Basic programming familiarity'],
        'audience' => ['Developers wanting full-stack breadth'],
        'modules' => [
            [
                'title' => 'Full-stack fundamentals',
                'lessons' => [
                    ['title' => 'Frontend, backend and the database, end to end', 'minutes' => 20, 'description' => 'How the three layers of a web application fit together.'],
                    ['title' => 'Deploying to a real server', 'minutes' => 25, 'description' => 'Getting a finished application onto Linux and serving real traffic.'],
                ],
            ],
        ],
        'live_classes' => [],
        'faqs' => [],
    ],

    [
        'slug' => 'cloud-architecture-aws-azure',
        'title' => 'Cloud Architecture & DevOps on AWS and Azure',
        'category' => 'data-engineering',
        'instructor' => 'arun-singh',
        'price' => 14900,
        'compare_at' => 18900,
        'level' => CourseLevel::Intermediate,
        'status' => CourseStatus::Archived,
        'short' => 'Designing and deploying production infrastructure across AWS and Azure, the way a cloud architect actually does it.',
        'description' => "A previously run programme on cloud architecture and deployment: designing infrastructure, automating it, and running it in production across both AWS and Azure.\n\nKept here for reference — this programme is not currently open for enrolment.",
        'outcomes' => [
            'Design production infrastructure on AWS and Azure',
            'Automate deployment instead of doing it by hand',
        ],
        'requirements' => ['Comfort with the command line'],
        'audience' => ['Engineers moving into infrastructure or DevOps'],
        'modules' => [
            [
                'title' => 'Cloud architecture fundamentals',
                'lessons' => [
                    ['title' => 'Designing infrastructure that survives real traffic', 'minutes' => 22, 'description' => 'Core architecture decisions across AWS and Azure.'],
                    ['title' => 'Automating deployment', 'minutes' => 24, 'description' => 'Moving from manual server changes to a repeatable deployment process.'],
                ],
            ],
        ],
        'live_classes' => [],
        'faqs' => [],
    ],

];
