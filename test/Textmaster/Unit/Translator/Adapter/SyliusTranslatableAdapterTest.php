<?php

namespace Textmaster\Unit\Translator\Adapter;

use Textmaster\Unit\Mock\MockTranslation;
use Textmaster\Translator\Adapter\SyliusTranslatableAdapter;

/**
 * @group sylius
 */
class SyliusTranslatableAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldSupportTranslatable()
    {
        $managerRegistryMock = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $translatableMock = $this->getMock('Sylius\Component\Resource\Model\TranslatableInterface');
        $adapter = new SyliusTranslatableAdapter($managerRegistryMock);

        $this->assertTrue($adapter->supports($translatableMock));
    }

    /**
     * @test
     */
    public function shouldNotSupportNotTranslatable()
    {
        $managerRegistryMock = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $translatableMock = $this->getMock('Sylius\Component\Resource\Model\NotTranslatableInterface');
        $adapter = new SyliusTranslatableAdapter($managerRegistryMock);

        $this->assertFalse($adapter->supports($translatableMock));
    }

    /**
     * @test
     */
    public function shouldCreate()
    {
        $managerRegistryMock = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $translatableMock = $this->getMock('Sylius\Component\Resource\Model\TranslatableInterface', array('getId', 'translate', 'hasTranslation', 'setCurrentLocale', 'setFallbackLocale', 'getTranslations', 'addTranslation', 'removeTranslation'));
        $translationMock = new MockTranslation();
        $documentMock = $this->getMock('Textmaster\Model\Document', array('getProject', 'save'), array(), '', false);
        $projectMock = $this->getMock('Textmaster\Model\Project', array('getLanguageFrom'), array(), '', false);

        $documentMock->expects($this->once())
            ->method('getProject')
            ->willReturn($projectMock);
        $documentMock->expects($this->once())
            ->method('save')
            ->willReturn($documentMock);

        $projectMock->expects($this->once())
            ->method('getLanguageFrom')
            ->willReturn('en');

        $translatableMock->expects($this->once())
            ->method('translate')
            ->willReturn($translationMock);
        $translatableMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $adapter = new SyliusTranslatableAdapter($managerRegistryMock);
        $adapter->create($translatableMock, array('name'), $documentMock);
    }

    /**
     * @test
     */
    public function shouldComplete()
    {
        $managerRegistryMock = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $objectManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectRepositoryMock = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $documentMock = $this->getMock('Textmaster\Model\DocumentInterface');
        $projectMock = $this->getMock('Textmaster\Model\ProjectInterface');

        $translatableMock = $this->getMock('Sylius\Component\Resource\Model\TranslatableInterface', array('getId', 'translate', 'hasTranslation', 'setCurrentLocale', 'setFallbackLocale', 'getTranslations', 'addTranslation', 'removeTranslation'));
        $translationMock = new MockTranslation();

        $documentMock->expects($this->once())
            ->method('getCustomData')
            ->willReturn(array('class' => 'My\Class', 'id' => 1));
        $documentMock->expects($this->once())
            ->method('getTranslatedContent')
            ->willReturn(array('name' => 'my translation'));
        $documentMock->expects($this->once())
            ->method('getProject')
            ->willReturn($projectMock);

        $managerRegistryMock->expects($this->exactly(2))
            ->method('getManagerForClass')
            ->will($this->returnValue($objectManagerMock));

        $objectManagerMock->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($objectRepositoryMock));

        $objectRepositoryMock->expects($this->once())
            ->method('find')
            ->willReturn($translatableMock);

        $translatableMock->expects($this->once())
            ->method('translate')
            ->willReturn($translationMock);

        $projectMock->expects($this->once())
            ->method('getLanguageTo')
            ->willReturn('fr');

        $adapter = new SyliusTranslatableAdapter($managerRegistryMock);
        $subject = $adapter->complete($documentMock);

        $this->assertSame($translatableMock, $subject);
        $this->assertSame('my translation', $translationMock->getName());
    }

    /**
     * @test
     */
    public function shouldCompare()
    {
        $managerRegistryMock = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');

        $objectManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectRepositoryMock = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $documentMock = $this->getMock('Textmaster\Model\DocumentInterface');
        $translatableMock = $this->getMock('Sylius\Component\Resource\Model\TranslatableInterface');
        $projectMock = $this->getMock('Textmaster\Model\ProjectInterface');
        $enTranslationMock = new MockTranslation();
        $enTranslationMock->setName('Name to translate');
        $frTranslationMock = new MockTranslation();

        $documentMock->expects($this->once())
            ->method('getCustomData')
            ->willReturn(array('class' => 'My\Class', 'id' => 1));
        $documentMock->expects($this->once())
            ->method('getOriginalContent')
            ->willReturn(array('name' => array('original_phrase' => 'Name to translate')));
        $documentMock->expects($this->once())
            ->method('getTranslatedContent')
            ->willReturn(array('name' => 'Le nom à traduire'));
        $documentMock->expects($this->exactly(2))
            ->method('getProject')
            ->willReturn($projectMock);

        $projectMock->expects($this->once())
            ->method('getLanguageFrom')
            ->willReturn('en');
        $projectMock->expects($this->once())
            ->method('getLanguageTo')
            ->willReturn('fr');

        $managerRegistryMock->expects($this->once())
            ->method('getManagerForClass')
            ->will($this->returnValue($objectManagerMock));

        $objectManagerMock->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($objectRepositoryMock));

        $objectRepositoryMock->expects($this->once())
            ->method('find')
            ->willReturn($translatableMock);

        $map = array(
            array('fr', $frTranslationMock),
            array('en', $enTranslationMock),
        );
        $translatableMock->expects($this->exactly(2))
            ->method('translate')
            ->will($this->returnValueMap($map));

        $adapter = new SyliusTranslatableAdapter($managerRegistryMock);
        $comparison = $adapter->compare($documentMock);

        $this->assertSame(array('name' => ''), $comparison['original']);
        $this->assertContains('Le nom à traduire', $comparison['translated']['name']);
    }
}
