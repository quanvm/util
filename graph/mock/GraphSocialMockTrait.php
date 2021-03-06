<?php

namespace go1\util\graph\mock;

use go1\util\GraphEdgeTypes;
use go1\util\group\GroupStatus;
use GraphAware\Neo4j\Client\Client;

trait GraphSocialMockTrait
{
    protected function addGraphUserTag(Client $client, int $userId, array $tagNames) {
        $hasAccount = GraphEdgeTypes::HAS_ACCOUNT;
        $hasGroup = GraphEdgeTypes::HAS_GROUP;
        $hasMember = GraphEdgeTypes::HAS_MEMBER;
        $hasTag = GraphEdgeTypes::HAS_TAG;

        $stack = $client->stack();
        $stack->push(
            "MATCH (u:User { id: {$userId} })"
            . " MATCH (u)-[r:{$hasTag}]->(:Tag)"
            . " DELETE r"
        );

        $stack->push(
            "MATCH (u:User)-[:{$hasAccount}]->(o:User)-[:{$hasGroup}]->(p:Group)-[:{$hasMember}]->(t:Tag)"
            . " WHERE p.name STARTS WITH 'portal:' AND u.id = {$userId} AND t.name IN {tagNames}"
            . " MERGE (u)-[:{$hasTag}]->(t)",
            ['tagNames' => $tagNames]
        );

        $client->runStack($stack);
    }

    protected function followGraph(Client $client, int $sourceId, int $targetId) {
        $hasFollowing = GraphEdgeTypes::HAS_FOLLOWING;
        $hasFollower = GraphEdgeTypes::HAS_FOLLOWER;

        $query = "MATCH (A:User { id: {$sourceId} })"
            . " MATCH (B:User { id: {$targetId} })"
            . " MERGE (A)-[r:{$hasFollowing}]->(B)"
            . " MERGE (B)-[rr:{$hasFollower}]->(A)";

        $client->run($query);
    }

    protected function createGraphGroup(Client $client, array $option) {
        $hasMember = GraphEdgeTypes::HAS_MEMBER;
        $hasGroupOwn = GraphEdgeTypes::HAS_GROUP_OWN;
        static $autoGroupId;

        $group = [
            'id'          => isset($option['id']) ? $option['id'] : ++$autoGroupId,
            'title'       => isset($option['title']) ? $option['title'] : uniqid('group'),
            'created'     => isset($option['created']) ? $option['created'] : time(),
            'visibility'  => isset($option['visibility']) ? $option['visibility'] : GroupStatus::PUBLIC,
            'instance_id' => isset($option['instance_id']) ? $option['instance_id'] : 0,
            'account_id'  => isset($option['account_id']) ? $option['account_id'] : 0,
        ];

        $stack = $client->stack();
        $stack->push("MERGE (g:Group { id: {$group['id']}, name: {name} }) SET g += {data}",
            [
                'name' => "group:{$group['id']}",
                'data' => [
                    'title'         => $group['title'],
                    'created'       => $group['created'],
                    'visibility'    => $group['visibility'],
                ]
            ]
        );

        $group['instance_id'] && $stack->push(
            " MATCH (g:Group { id: {$group['id']}, name: {groupName} })"
            . "MERGE (p:Group { name: {portalName} })"
            . " MERGE (p)-[:{$hasGroupOwn}]->(g)"
            . " MERGE (g)-[:{$hasMember}]->(p)",
            ['portalName' => "portal:{$group['instance_id']}", 'groupName' => "group:{$group['id']}"]
        );

        $group['account_id'] && $stack->push(
            " MATCH (g:Group { id: {$group['id']}, name: {groupName}})"
            . "MERGE (account:User { id: {$group['account_id']} })"
            . " MERGE (account)-[:{$hasGroupOwn}]->(g)"
            . " MERGE (g)-[:{$hasMember}]->(account)",
            ['groupName' => "group:{$group['id']}"]
        );

        $client->runStack($stack);

        return $autoGroupId;
    }

    protected function addGraphUserGroup(Client $client, int $accountId, int $groupId) {
        $hasGroup = GraphEdgeTypes::HAS_GROUP;
        $hasMember = GraphEdgeTypes::HAS_MEMBER;

        $query = "MATCH (g:Group { id: {$groupId}, name: {groupName} })"
            . " MERGE (acc:User { id: {$accountId} })"
            . " MERGE (acc)-[:{$hasGroup}]->(g)"
            . " MERGE (g)-[:{$hasMember}]->(acc)";

        $client->run($query, ['groupName' => "group:{$groupId}"]);
    }

    protected function addGraphPortalGroup(Client $client, int $portalId, int $groupId) {
        $hasGroup = GraphEdgeTypes::HAS_GROUP;
        $hasMember = GraphEdgeTypes::HAS_MEMBER;

        $query = " MATCH (g:Group { id: {$groupId}, name: {groupName} })"
            . "MERGE (p:Group { name: {portalName} })"
            . " MERGE (p)-[:{$hasGroup}]->(g)"
            . " MERGE (g)-[:{$hasMember}]->(p)";

        $client->run($query, ['portalName' => "portal:{$portalId}", 'groupName' => "group:{$groupId}"]);
    }

    protected function addGraphLoGroup(Client $client, int $loId, int $groupId) {
        $hasSharedGroup = GraphEdgeTypes::HAS_SHARED_GROUP;
        $hasSharedLo = GraphEdgeTypes::HAS_SHARED_LO;

        $query = " MATCH (g:Group { id: {$groupId}, name: {groupName} })"
            . "MERGE (lo:Group { name: {loName} })"
            . " MERGE (lo)-[:{$hasSharedGroup}]->(g)"
            . " MERGE (g)-[:{$hasSharedLo}]->(lo)";

        $client->run($query, ['loName' => "lo:{$loId}", 'groupName' => "group:{$groupId}"]);
    }

    protected function addGraphNoteGroup(Client $client, string $uuid, int $groupId) {
        $hasGroup = GraphEdgeTypes::HAS_GROUP;
        $hasMember = GraphEdgeTypes::HAS_MEMBER;

        $query = " MATCH (g:Group { id: {$groupId}, name: {groupName} })"
            . "MERGE (n:Note { uuid: {uuid} })"
            . " MERGE (n)-[:{$hasGroup}]->(g)"
            . " MERGE (g)-[:{$hasMember}]->(n)";

        $client->run($query, ['uuid' => "{$uuid}", 'groupName' => "group:{$groupId}"]);
    }
}
