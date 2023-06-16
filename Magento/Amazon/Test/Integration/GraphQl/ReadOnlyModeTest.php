<?php

namespace Magento\Amazon\Test\Integration\GraphQl;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ReadOnlyModeTest extends BaseTest
{
    public function testReadOnlyModeReturnsFalseByDefault(): void
    {
        $gql = $this->getFixtureData('get_magento_settings.graphql');
        $result = $this->query($gql);
        $this->assertHasNoError($result);
        $readOnlyMode = $this->getChildNode($result, 'data.magentoSettings.readOnlyMode');
        $this->assertFalse($readOnlyMode, 'readOnlyMode must be false by default');
    }

    /**
     * @magentoConfigFixture default/saleschannels/general/read_only 0
     */
    public function testReadOnlyModeReturnsFalseWhenDisabled(): void
    {
        $gql = $this->getFixtureData('get_magento_settings.graphql');
        $result = $this->query($gql);
        $this->assertHasNoError($result);
        $readOnlyMode = $this->getChildNode($result, 'data.magentoSettings.readOnlyMode');
        $this->assertFalse($readOnlyMode, 'readOnlyMode must be false when disabled');
    }

    /**
     * @magentoConfigFixture default/saleschannels/general/read_only 1
     */
    public function testReadOnlyModeReturnsTrueWhenEnabled(): void
    {
        $gql = $this->getFixtureData('get_magento_settings.graphql');
        $result = $this->query($gql);
        $this->assertHasNoError($result);
        $readOnlyMode = $this->getChildNode($result, 'data.magentoSettings.readOnlyMode');
        $this->assertTrue($readOnlyMode, 'readOnlyMode must be true when enabled');
    }

    /**
     * @magentoConfigFixture default/saleschannels/general/read_only 0
     */
    public function testReadOnlyModeCouldBeEnabledWithMutation(): void
    {
        $gql = $this->getFixtureData('enable_read_only_mode.graphql');
        $result = $this->query($gql);
        $this->assertHasNoError($result);
        $mutationResult = $this->getChildNode($result, 'data.setReadOnlyMode');
        $this->assertTrue($mutationResult, 'Mutation must return true by the contract if there are no errors');

        $gql = $this->getFixtureData('get_magento_settings.graphql');
        $result = $this->query($gql);
        $this->assertHasNoError($result);
        $readOnlyMode = $this->getChildNode($result, 'data.magentoSettings.readOnlyMode');
        $this->assertTrue($readOnlyMode, 'readOnlyMode must have been enabled');
    }

    /**
     * @magentoConfigFixture default/saleschannels/general/read_only 1
     */
    public function testReadOnlyModeCouldBeDisabledWithMutation(): void
    {
        $gql = $this->getFixtureData('enable_read_only_mode.graphql');
        $result = $this->query($gql);
        $this->assertHasNoError($result);
        $mutationResult = $this->getChildNode($result, 'data.setReadOnlyMode');
        $this->assertTrue($mutationResult, 'Mutation must return true by the contract if there are no errors');

        $gql = $this->getFixtureData('get_magento_settings.graphql');
        $result = $this->query($gql);
        $this->assertHasNoError($result);
        $readOnlyMode = $this->getChildNode($result, 'data.magentoSettings.readOnlyMode');
        $this->assertTrue($readOnlyMode, 'readOnlyMode must have been disabled');
    }
}
