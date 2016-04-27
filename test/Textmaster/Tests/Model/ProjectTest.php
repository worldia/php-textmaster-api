<?php

/*
 * This file is part of the Textmaster Api v1 client package.
 *
 * (c) Christian Daguerre <christian@daguer.re>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Textmaster\Tests\Model;

use Textmaster\Model\DocumentInterface;
use Textmaster\Model\Project;
use Textmaster\Model\ProjectInterface;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    protected $clientMock;

    public function setUp()
    {
        parent::setUp();

        $showValues = array(
            'id' => '123456',
            'name' => 'Project 1',
            'status' => ProjectInterface::STATUS_IN_CREATION,
            'ctype' => ProjectInterface::ACTIVITY_TRANSLATION,
            'language_from' => 'fr',
            'language_to' => 'en',
            'category' => 'C014',
            'project_briefing' => 'Lorem ipsum...',
            'options' => array('language_level' => 'premium'),
        );
        $updateValues = array(
            'id' => '123456',
            'name' => 'Project Beta',
            'status' => ProjectInterface::STATUS_IN_CREATION,
            'ctype' => ProjectInterface::ACTIVITY_TRANSLATION,
            'language_from' => 'fr',
            'language_to' => 'en',
            'category' => 'C014',
            'project_briefing' => 'Lorem ipsum...',
            'options' => array('language_level' => 'premium'),
        );

        $clientMock = $this->getMock('Textmaster\Client', array('api'));
        $projectApiMock = $this->getMock('Textmaster\Api\Project', array('show', 'update'), array($clientMock));
        $documentApiMock = $this->getMock('Textmaster\Api\FilterableApiInterface', array('filter', 'getClient'));

        $clientMock->method('api')
            ->willReturn($projectApiMock);

        $projectApiMock->method('show')
            ->willReturn($showValues);

        $projectApiMock->method('update')
            ->willReturn($updateValues);

        $projectApiMock->method('documents')
            ->willReturn($documentApiMock);

        $this->clientMock = $clientMock;
    }

    /**
     * @test
     */
    public function shouldCreateEmpty()
    {
        $name = 'Project 1';
        $status = ProjectInterface::STATUS_IN_CREATION;
        $activity = ProjectInterface::ACTIVITY_TRANSLATION;
        $languageFrom = 'fr';
        $languageTo = 'en';
        $category = 'C014';
        $briefing = 'Lorem ipsum...';
        $options = array('language_level' => 'premium');

        $project = new Project($this->clientMock);
        $project
            ->setName($name)
            ->setActivity($activity)
            ->setLanguageFrom($languageFrom)
            ->setLanguageTo($languageTo)
            ->setCategory($category)
            ->setBriefing($briefing)
            ->setOptions($options)
        ;

        $this->assertNull($project->getId());
        $this->assertSame($name, $project->getName());
        $this->assertSame($status, $project->getStatus());
        $this->assertSame($activity, $project->getActivity());
        $this->assertSame($languageFrom, $project->getLanguageFrom());
        $this->assertSame($languageTo, $project->getLanguageTo());
        $this->assertSame($category, $project->getCategory());
        $this->assertSame($briefing, $project->getBriefing());
        $this->assertSame($options, $project->getOptions());
    }

    /**
     * @test
     */
    public function shouldCreateFromValues()
    {
        $id = '123456';
        $name = 'Project 1';
        $status = ProjectInterface::STATUS_IN_CREATION;
        $activity = ProjectInterface::ACTIVITY_TRANSLATION;
        $languageFrom = 'fr';
        $languageTo = 'en';
        $category = 'C014';
        $briefing = 'Lorem ipsum...';
        $options = array('language_level' => 'premium');

        $values = array(
            'id' => $id,
            'name' => $name,
            'status' => $status,
            'ctype' => $activity,
            'language_from' => $languageFrom,
            'language_to' => $languageTo,
            'category' => $category,
            'project_briefing' => $briefing,
            'options' => $options,
        );

        $project = new Project($this->clientMock, $values);

        $this->assertSame($id, $project->getId());
        $this->assertSame($name, $project->getName());
        $this->assertSame($status, $project->getStatus());
        $this->assertSame($activity, $project->getActivity());
        $this->assertSame($languageFrom, $project->getLanguageFrom());
        $this->assertSame($languageTo, $project->getLanguageTo());
        $this->assertSame($category, $project->getCategory());
        $this->assertSame($briefing, $project->getBriefing());
        $this->assertSame($options, $project->getOptions());
    }

    /**
     * @test
     */
    public function shouldUpdate()
    {
        $name = 'Project 1';

        $project = new Project($this->clientMock, '123456');

        $this->assertSame($name, $project->getName());

        $project->setName('Project Beta');
        $project->save();

        $this->assertSame('Project Beta', $project->getName());
    }

    /**
     * @test
     */
    public function shouldGetDocuments()
    {
        $project = new Project($this->clientMock, '123456');
        $pager = $project->getDocuments();

        $this->assertSame('Pagerfanta\Pagerfanta', get_class($pager));
    }

    /**
     * @test
     */
    public function shouldCreateDocument()
    {
        $project = new Project($this->clientMock, '123456');
        $document = $project->createDocument();

        $this->assertSame('Textmaster\Model\Document', get_class($document));
        $this->assertSame(DocumentInterface::STATUS_IN_CREATION, $document->getStatus());
    }

    /**
     * @test
     */
    public function shouldGetAllowedActivities()
    {
        $allowedActivities = array(
            ProjectInterface::ACTIVITY_COPYWRITING,
            ProjectInterface::ACTIVITY_PROOFREADING,
            ProjectInterface::ACTIVITY_TRANSLATION,
        );

        $this->assertSame($allowedActivities, Project::getAllowedActivities());
    }

    /**
     * @test
     * @expectedException \Textmaster\Exception\ObjectImmutableException
     */
    public function shouldBeImmutable()
    {
        $values = array(
            'id' => 'ID-IMMUTABLE',
            'status' => ProjectInterface::STATUS_IN_PROGRESS,
        );

        $project = new Project($this->clientMock, $values);
        $project->setName('New name');
    }

    /**
     * @test
     * @expectedException \Textmaster\Exception\InvalidArgumentException
     */
    public function shouldNotSetWrongActivity()
    {
        $project = new Project($this->clientMock, '123456');
        $project->setActivity('wrong activity name');
    }

    /**
     * @test
     * @expectedException \Textmaster\Exception\BadMethodCallException
     */
    public function shouldNotCreateDocumentOnUnsaved()
    {
        $project = new Project($this->clientMock);
        $project->createDocument();
    }
}
