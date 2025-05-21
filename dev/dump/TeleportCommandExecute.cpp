// Executes the teleport command, handling the movement of entities to specified coordinates or other entities, with optional rotation and block collision checks.
// Parameters:
// - command: Pointer to the TeleportCommand instance containing command data (e.g., destination, rotation, facing direction).
// - origin: Pointer to the CommandOrigin, specifying the source of the command (e.g., player, console).
// - output: Pointer to the CommandOutput, used to store success or error messages resulting from command execution.
void __fastcall TeleportCommand::execute(TeleportCommand* command, const CommandOrigin* origin, CommandOutput* output)
{
    // Local variables for command execution and data storage
    CommandOutput* outputPtr = output;              // Store output pointer for consistent access
    const CommandOrigin* originPtr = origin;        // Store origin pointer for consistent access
    Actor* targetActor = nullptr;                  // Target actor for teleportation
    Vec3 destinationPos = {0.0f, 0.0f, 0.0f};      // Destination position for teleportation
    std::string positionStr;                       // String representation of destination position
    int dimensionId = VanillaDimensions::Undefined; // Dimension ID for teleportation
    bool hasValidTargets = false;                  // Flag to check if valid targets exist
    CommandArea* commandArea = nullptr;             // Area for teleportation checks
    std::vector<Actor*> successfulTeleports;       // List of actors successfully teleported
    std::vector<Actor*> unsafeTeleports;           // List of actors with unsafe teleport attempts
    std::vector<Actor*> farTeleports;              // List of actors with far teleport attempts
    std::string errorMsgBuffer;                    // Buffer for error messages
    std::string successMsgBuffer;                  // Buffer for success messages
    std::vector<CommandOutputParameter> outputParams; // Parameters for output messages
    char tempBuffer[40];                           // Temporary buffer for string operations
    char tempRotationBuffer[16];                   // Buffer for rotation data
    char tempBlockPos[12];                         // Buffer for block position
    char tempChunkPos[8];                          // Buffer for chunk position
    bool useRotation = false;                      // Flag for rotation usage
    int commandVersion = 0;                        // Command version for validation
    RotationCommandUtils::RotationData rotationData; // Rotation data for teleportation
    __int64* facingDirectionPtr = nullptr;         // Pointer for facing direction data

    // Check if the command origin has permission to execute the teleport command
    if (!(*(unsigned __int8 (__fastcall**)(const CommandOrigin*, const CommandOrigin*))(*(_QWORD*)origin + 120LL))(originPtr, originPtr))
    {
        // Prepare error message for insufficient permissions
        errorMsgBuffer.resize(32);
        strcpy(errorMsgBuffer.data(), "commands.tp.permission");
        CommandOutput::error(outputPtr, &errorMsgBuffer, nullptr);

        // Clean up error message buffer
        if (errorMsgBuffer.size() >= 0x10)
        {
            __int64 bufferPtr = *(_QWORD*)errorMsgBuffer.data();
            if (errorMsgBuffer.size() + 1 >= 0x1000)
            {
                bufferPtr = *(_QWORD*)(errorMsgBuffer.data() - 8);
                if ((unsigned __int64)(errorMsgBuffer.data() - bufferPtr - 8) > 0x1F)
                    _invalid_parameter_noinfo_noreturn();
            }
            std::_Return_temporary_buffer<unsigned int>(bufferPtr);
        }
        errorMsgBuffer.clear();
        return;
    }

    // Get the list of target actors for teleportation
    CommandSelectorResults<Actor> targetResults;
    CommandSelector<Actor>::results((char*)command + 96, &targetResults, originPtr);

    // Check if there are valid targets to teleport
    hasValidTargets = Command::checkHasTargets<Actor>(&targetResults, outputPtr);
    if (!hasValidTargets)
    {
        // Clean up target results
        auto targetIterator = targetResults.end();
        if (targetIterator)
        {
            if (_InterlockedExchangeAdd(targetIterator + 2, 0xFFFFFFFF) == 1)
            {
                (**(void (__fastcall***)(volatile signed __int32*))targetIterator)(targetIterator);
                if (_InterlockedExchangeAdd(targetIterator + 3, 0xFFFFFFFF) == 1)
                    (*(void (__fastcall**)(volatile signed __int32*))(*(_QWORD*)targetIterator + 8LL))(targetIterator);
            }
        }
        return;
    }

    // Initialize dimension ID
    if (*((int*)command + 2) >= 15)
    {
        __int64 levelPtr = (*(__int64 (__fastcall**)(const CommandOrigin*))(*(_QWORD*)originPtr + 56LL))(originPtr);
        if (levelPtr)
            dimensionId = *(_DWORD*)(*(__int64 (__fastcall**)(__int64, char*))(*(_QWORD*)levelPtr + 16LL))(levelPtr, tempBuffer);
    }

    // Determine teleport destination (coordinates or entity)
    if (*((bool*)command + 748))
    {
        // Get destination coordinates
        Vec3 tempPos;
        float* posPtr = CommandPosition::getPosition((char*)command + 496, tempBlockPos, *((unsigned int*)command + 2), originPtr, &tempPos);
        destinationPos.x = posPtr[0];
        destinationPos.y = posPtr[1];
        destinationPos.z = posPtr[2];

        // Adjust coordinates to block center if command version is less than 3
        if (*((int*)command + 2) < 3)
        {
            BlockPos blockPos;
            BlockPos::BlockPos(&blockPos, &destinationPos);
            destinationPos.x = blockPos.x + 0.5f;
            destinationPos.y = blockPos.y;
            destinationPos.z = blockPos.z + 0.5f;
        }

        // Format position string for output
        positionStr.resize(32);
        Util::format(&positionStr, "%0.2f, %0.2f, %0.2f", destinationPos.x, destinationPos.y, destinationPos.z);
    }
    else
    {
        // Get destination entity
        CommandSelectorResults<Actor> destResults;
        CommandSelector<Actor>::results((char*)command + 296, &destResults, originPtr);

        if (Command::checkHasTargets<Actor>(&destResults, outputPtr))
        {
            if (CommandSelectorResults<Player>::size(&destResults) == 1)
            {
                targetActor = CommandSelectorResults<Actor>::get(&destResults);
                const Vec3* actorPos = Actor::getPosition(targetActor);
                destinationPos = *actorPos;
                destinationPos.y = Actor::getAABB(targetActor)->center.y;

                // Get actor name for output
                positionStr = CommandUtils::getActorName(tempBuffer, targetActor);
                dimensionId = *(_DWORD*)Actor::getDimensionId(targetActor, tempBuffer);
            }
            else
            {
                // Handle error for multiple destination entities
                errorMsgBuffer.resize(32);
                CommandOutput::error(outputPtr, &errorMsgBuffer, nullptr);
                if (errorMsgBuffer.size() >= 0x10)
                {
                    __int64 bufferPtr = *(_QWORD*)errorMsgBuffer.data();
                    if (errorMsgBuffer.size() + 1 >= 0x1000)
                    {
                        bufferPtr = *(_QWORD*)(errorMsgBuffer.data() - 8);
                        if ((unsigned __int64)(errorMsgBuffer.data() - bufferPtr - 8) > 0x1F)
                            _invalid_parameter_noinfo_noreturn();
                    }
                    std::_Return_temporary_buffer<unsigned int>(bufferPtr);
                }
                errorMsgBuffer.clear();
            }

            // Clean up destination results
            auto destIterator = destResults.end();
            if (destIterator)
            {
                if (_InterlockedExchangeAdd(destIterator + 2, 0xFFFFFFFF) == 1)
                {
                    (**(void (__fastcall***)(volatile signed __int32*))destIterator)(destIterator);
                    if (_InterlockedExchangeAdd(destIterator + 3, 0xFFFFFFFF) == 1)
                        (*(void (__fastcall**)(volatile signed __int32*))(*(_QWORD*)destIterator + 8LL))(destIterator);
                }
            }
        }
    }

    // Get facing direction for teleportation
    int facingDirection = TeleportCommand::getFacingDirection(command, originPtr, outputPtr, &facingDirectionPtr, targetActor);
    if (facingDirection != 2)
    {
        facingDirectionPtr = (facingDirection == 0) ? nullptr : facingDirectionPtr;

        // Get command area for teleportation checks
        TeleportCommand::getCommandAreaForTargets(
            &commandArea, originPtr, &targetResults, &destinationPos, *((int*)command + 2), !*((bool*)command + 751));

        if (commandArea)
        {
            // Iterate through target actors
            for (auto it = CommandSelectorResults<Actor>::begin(&targetResults); it != CommandSelectorResults<Actor>::end(&targetResults); ++it)
            {
                Actor* currentActor = *it;

                // Check dimension compatibility
                if (*((int*)command + 2) < 3 && dimensionId != VanillaDimensions::Undefined &&
                    *(_DWORD*)Actor::getDimensionId(currentActor, tempBuffer) != dimensionId)
                {
                    errorMsgBuffer.resize(32);
                    CommandOutput::error(outputPtr, &errorMsgBuffer, nullptr);
                    if (errorMsgBuffer.size() >= 0x10)
                    {
                        __int64 bufferPtr = *(_QWORD*)errorMsgBuffer.data();
                        if (errorMsgBuffer.size() + 1 >= 0x1000)
                        {
                            bufferPtr = *(_QWORD*)(errorMsgBuffer.data() - 8);
                            if ((unsigned __int64)(errorMsgBuffer.data() - bufferPtr - 8) > 0x1F)
                                _invalid_parameter_noinfo_noreturn();
                        }
                        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
                    }
                    errorMsgBuffer.clear();
                    continue;
                }

                // Validate teleport location
                BlockPos blockPos;
                BlockPos::BlockPos(&blockPos, &destinationPos);
                __int64 levelPtr = (*(__int64 (__fastcall**)(const CommandOrigin*))(*(_QWORD*)originPtr + 48LL))(originPtr);
                __int64 levelData = (*(__int64 (__fastcall**)(__int64))(*(_QWORD*)levelPtr + 1184LL))(levelPtr);

                if ((unsigned int)LevelData::getGenerator(levelData) ||
                    (Dimension* dim = Actor::getDimensionConst(currentActor),
                     ChunkSource* chunkSource = Dimension::getChunkSource(dim),
                     (*(unsigned __int8 (__fastcall**)(ChunkSource*, char*))(*(_QWORD*)chunkSource + 184LL))(chunkSource, tempChunkPos)))
                {
                    // Perform block collision check if required
                    if (*((bool*)command + 751))
                    {
                        BlockSource* blockSource = CommandArea::getDimensionBlockSource(commandArea);
                        int teleportResult = TeleportCommandHelpers::actorToLocationTeleportAnalysis(blockSource, currentActor, &destinationPos);
                        if (teleportResult == 1)
                        {
                            successfulTeleports.push_back(currentActor);
                            continue;
                        }
                        if (teleportResult == 2)
                        {
                            unsafeTeleports.push_back(currentActor);
                            continue;
                        }
                    }

                    // Handle backward compatibility for armor stands
                    TeleportCommand::doArmorStandTeleportBackwardCompability(command, currentActor, targetActor);

                    // Handle rotation
                    if (*((bool*)command + 749))
                    {
                        char* rotationSrc = Command::shouldUseCommandOriginRotation(originPtr, *((int*)command + 2))
                                          ? (char*)(*(__int64 (__fastcall**)(const CommandOrigin*, char*))(*(_QWORD*)originPtr + 40LL))(originPtr, tempBlockPos)
                                          : tempRotationBuffer;
                        rotationData = RotationCommandUtils::RotationData::RotationData(
                            tempRotationBuffer, (char*)command + 736, (char*)command + 728, rotationSrc);
                        useRotation = true;
                    }

                    // Perform teleportation
                    (*(void (__fastcall**)(__int64, Actor*, Vec3*, __int64**, int*, RotationCommandUtils::RotationData*, int*))(*(_QWORD*)command + 88LL))(
                        command, currentActor, &destinationPos, &facingDirectionPtr, &dimensionId, &rotationData, &commandVersion);

                    // Record successful teleport
                    successfulTeleports.push_back(currentActor);

                    // Display message for armor stands
                    if ((unsigned int)Actor::getEntityTypeId(currentActor) == 319)
                    {
                        outputParams.clear();
                        outputParams.emplace_back(successfulTeleports);
                        outputParams.emplace_back(positionStr);
                        successMsgBuffer = "commands.tp.success";
                        CommandOutput::success(outputPtr, &successMsgBuffer, &outputParams);
                        CommandUtils::displayLocalizableMessage(true, currentActor, &successMsgBuffer, outputParams);
                        outputParams.clear();
                        successMsgBuffer.clear();
                    }

                    // Log destination to output
                    if (*(_DWORD*)outputPtr == 4)
                    {
                        std::string destKey = "destination";
                        CommandPropertyBag::set(*(_QWORD*)(outputPtr + 8LL), destKey.c_str(), &destinationPos);
                    }

                    // Add victim to result list
                    std::string victimKey = "victim";
                    CommandOutput::addToResultList(outputPtr, &victimKey, currentActor);
                }
                else
                {
                    // Handle out-of-world teleport error
                    errorMsgBuffer = "commands.tp.outOfWorld";
                    CommandOutput::error(outputPtr, &errorMsgBuffer, nullptr);
                    if (errorMsgBuffer.size() >= 0x10)
                    {
                        __int64 bufferPtr = *(_QWORD*)errorMsgBuffer.data();
                        if (errorMsgBuffer.size() + 1 >= 0x1000)
                        {
                            bufferPtr = *(_QWORD*)(errorMsgBuffer.data() - 8);
                            if ((unsigned __int64)(errorMsgBuffer.data() - bufferPtr - 8) > 0x1F)
                                _invalid_parameter_noinfo_noreturn();
                        }
                        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
                    }
                    errorMsgBuffer.clear();
                }
            }

            // Clean up iterator
            auto endIterator = CommandSelectorResults<Actor>::end(&targetResults);
            if (endIterator)
            {
                if (_InterlockedExchangeAdd(endIterator + 2, 0xFFFFFFFF) == 1)
                {
                    (**(void (__fastcall***)(volatile signed __int32*))endIterator)(endIterator);
                    if (_InterlockedExchangeAdd(endIterator + 3, 0xFFFFFFFF) == 1)
                        (*(void (__fastcall**)(volatile signed __int32*))(*(_QWORD*)endIterator + 8LL))(endIterator);
                }
            }
        }
        else
        {
            // Handle invalid command area
            for (auto it = CommandSelectorResults<Actor>::begin(&targetResults); it != CommandSelectorResults<Actor>::end(&targetResults); ++it)
                unsafeTeleports.push_back(*it);
        }

        // Report successful teleports
        if (!successfulTeleports.empty())
        {
            outputParams.clear();
            if (targetActor)
            {
                outputParams.emplace_back(successfulTeleports);
                outputParams.emplace_back(targetActor);
                successMsgBuffer = "commands.tp.success";
            }
            else
            {
                outputParams.emplace_back(successfulTeleports);
                outputParams.emplace_back(destinationPos.x);
                outputParams.emplace_back(destinationPos.y);
                outputParams.emplace_back(destinationPos.z);
                successMsgBuffer = "commands.tp.success.coordinate";
            }
            CommandOutput::success(outputPtr, &successMsgBuffer, &outputParams);
            outputParams.clear();
            successMsgBuffer.clear();
        }

        // Report unsafe teleports
        if (!unsafeTeleports.empty())
        {
            outputParams.clear();
            outputParams.emplace_back(unsafeTeleports);
            outputParams.emplace_back(positionStr);
            errorMsgBuffer = "commands.tp.safeTeleportFail";
            CommandOutput::error(outputPtr, &errorMsgBuffer, &outputParams);
            outputParams.clear();
            errorMsgBuffer.clear();
        }

        // Report far teleports
        if (!farTeleports.empty())
        {
            outputParams.clear();
            outputParams.emplace_back(farTeleports);
            outputParams.emplace_back(positionStr);
            errorMsgBuffer = "commands.tp.far";
            CommandOutput::error(outputPtr, &errorMsgBuffer, &outputParams);
            outputParams.clear();
            errorMsgBuffer.clear();
        }

        // Clean up command area
        if (commandArea)
        {
            __1__unique_ptr_V__StrictTickingSystemFunctionAdapter__MP6AXV__ViewT_VStrictEntityContext__U__Include_V__FlagComponent_UActorMovementTickNeededFlag____V__FlagComponent_ULavaTravelFlag________CBUMovementAttributesComponent__UMobTravelComponent_____Z1_tickLavaTravelSystem_LavaTravelSystem__SAX0_Z_M__VS__U__default_delete_V__StrictTickingSystemFunctionAdapter__MP6AXV__ViewT_VStrictEntityContext__U__Include_V__FlagComponent_UActorMovementTickNeededFlag____V__FlagComponent_ULavaTravelFlag________CBUMovementAttributesComponent__UMobTravelComponent_____Z1_tickLavaTravelSystem_LavaTravelSystem__SAX0_Z_M__VS___std___std__QEAA_XZ(commandArea);
            std::_Return_temporary_buffer<unsigned int>(commandArea);
        }

        // Clean up teleport lists
        for (auto& list : { &farTeleports, &unsafeTeleports, &successfulTeleports })
        {
            if (!list->empty())
            {
                __int64 bufferPtr = (unsigned __int64)list->data();
                if (list->size() * 8 >= 0x1000)
                {
                    bufferPtr = *(_QWORD*)(list->data() - 8);
                    if ((unsigned __int64)(list->data() - bufferPtr - 8) > 0x1F)
                        _invalid_parameter_noinfo_noreturn();
                }
                std::_Return_temporary_buffer<unsigned int>(bufferPtr);
                list->clear();
            }
        }
    }

    // Clean up position string
    if (positionStr.size() >= 0x10)
    {
        __int64 bufferPtr = *(_QWORD*)positionStr.data();
        if (positionStr.size() + 1 >= 0x1000)
        {
            bufferPtr = *(_QWORD*)(positionStr.data() - 8);
            if ((unsigned __int64)(positionStr.data() - bufferPtr - 8) > 0x1F)
                _invalid_parameter_noinfo_noreturn();
        }
        std::_Return_temporary_buffer<unsigned int>(bufferPtr);
    }
    positionStr.clear();

    // Clean up target results
    auto finalIterator = targetResults.end();
    if (finalIterator)
    {
        if (_InterlockedExchangeAdd(finalIterator + 2, 0xFFFFFFFF) == 1)
        {
            (**(void (__fastcall***)(volatile signed __int32*))finalIterator)(finalIterator);
            if (_InterlockedExchangeAdd(finalIterator + 3, 0xFFFFFFFF) == 1)
                (*(void (__fastcall**)(volatile signed __int32*))(*(_QWORD*)finalIterator + 8LL))(finalIterator);
        }
    }
}