<?php

namespace PHPCR\Tests\PhpcrUtils;

require_once(__DIR__ . '/../../inc/BaseCase.php');

use PHPCR\PropertyType;
use PHPCR\Util\NodeHelper;

use PHPCR\Test\BaseCase;

class PurgeTest extends BaseCase
{
    public static function setupBeforeClass($fixtures = '11_Import/empty')
    {
        parent::setupBeforeClass($fixtures);
    }

    protected function setUp()
    {
        if (! class_exists('PHPCR\Util\NodeHelper')) {
            $this->markTestSkipped('This testbed does not have phpcr-utils available');
        }
        parent::setUp();
    }

    public function testPurge()
    {
        $systemNodeCount = count($this->session->getRootNode()->getNodes());

        $a = $this->session->getRootNode()->addNode('a', 'nt:unstructured');
        $a->addMixin('mix:referenceable');
        $b = $this->session->getRootNode()->addNode('b', 'nt:unstructured');
        $b->addMixin('mix:referenceable');
        $this->session->save();

        $a->setProperty('ref', $b, PropertyType::REFERENCE);
        $b->setProperty('ref', $a, PropertyType::REFERENCE);
        $this->session->save();

        NodeHelper::purgeWorkspace($this->session);
        if ($this->session->getWorkspace()->getName() == 'crx.default') {
            // if we would continue, we would delete all content from the only real workspace
            $this->markTestIncomplete('TODO: how to test this with crx where we have no workspaces?');
        }
        $this->session->save();

        // if there where system nodes, they should still be here
        $this->assertCount($systemNodeCount, $this->session->getRootNode()->getNodes());
    }
}
