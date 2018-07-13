<?php

use PHPUnit\Framework\TestCase;
use Tighten\Linters\NoUnusedImports;
use Tighten\TLint;

class NoUnusedImportsTest extends TestCase
{
    /** @test */
    public function catches_unused_import()
    {
        $file = <<<file
<?php

use Test\ThingA;
use Test\ThingB;

\$testA = new ThingB;
\$testB = ThingB::class;
\$testC = ThingB::make();

file;

        $lints = (new TLint)->lint(
            new NoUnusedImports($file)
        );

        $this->assertEquals(3, $lints[0]->getNode()->getLine());
    }

    /** @test */
    public function does_not_trigger_when_import_used_to_extend_class()
    {
        $file = <<<file
<?php

use Test\ThingA;

class ThingC extends ThingA
{

}

file;

        $lints = (new TLint)->lint(
            new NoUnusedImports($file)
        );

        $this->assertEmpty($lints);
    }

    /** @test */
    public function does_not_trigger_when_import_used_as_typehint()
    {
        $file = <<<file
<?php

use Test\ThingA;

\$func = function (ThingA \$thingA) {
    return 1;
};

file;

        $lints = (new TLint)->lint(
            new NoUnusedImports($file)
        );

        $this->assertEmpty($lints);
    }

    /** @test */
    public function does_not_trigger_when_import_used_as_typehint_in_catch()
    {
        $file = <<<file
<?php

use Test\ThingA;

try {

} catch (ThingA \$e) {

}

file;

        $lints = (new TLint)->lint(
            new NoUnusedImports($file)
        );

        $this->assertEmpty($lints);
    }

    /** @test */
    public function does_not_trigger_when_import_used_in_instanceof_check()
    {
        $file = <<<file
<?php

use Test\ThingA;

if (\$thing instanceof ThingA) {

}

file;

        $lints = (new TLint)->lint(
            new NoUnusedImports($file)
        );

        $this->assertEmpty($lints);
    }

    /** @test */
    public function does_not_trigger_when_import_used_in_trait_use()
    {
        $file = <<<file
<?php

use Test\ThingA;

class Thing
{
    use ThingA;
    use ThingB;
}

file;

        $lints = (new TLint)->lint(
            new NoUnusedImports($file)
        );

        $this->assertEmpty($lints);
    }

    /** @test */
    public function does_not_trigger_when_import_is_callable()
    {
        $file = <<<file
<?php

use Closure;

Closure::fromCallable([\$test, 'isTraitUse']);

file;

        $lints = (new TLint)->lint(
            new NoUnusedImports($file)
        );

        $this->assertEmpty($lints);
    }

    /** @test */
    public function does_not_trigger_when_import_is_aliased()
    {
        $file = <<<file
<?php

use Test\ThingA as ThingB;

\$testA = new ThingB;
\$testB = ThingB::class;
\$testC = ThingB::make();

file;

        $lints = (new TLint)->lint(
            new NoUnusedImports($file)
        );

        $this->assertEmpty($lints);
    }

    /** @test */
    public function handles_variable_class_static_const()
    {
        $file = <<<file
<?php

\$var::do();

file;

        $lints = (new TLint)->lint(
            new NoUnusedImports($file)
        );

        $this->assertEmpty($lints);
    }

    /** @test */
    public function catches_unused_import_handles_variable_class_instantiation()
    {
        $file = <<<file
<?php

use Test\ThingA;
use Test\ThingB;

\$testA = new ThingB;
\$testB = ThingB::class;
\$testC = new \$testB;

file;

        $lints = (new TLint)->lint(
            new NoUnusedImports($file)
        );

        $this->assertEquals(3, $lints[0]->getNode()->getLine());
    }
}
