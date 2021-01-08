<?php

declare(strict_types=1);

namespace Irwan\Slapper_UI;

/*
 *
 * Plugin Info
 * Dibuat Oleh Irwan
 * Version 2
 *
 */

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;

use pocketmine\math\Vector3;
use pocketmine\level\Position;

use pocketmine\level\Level;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\utils\Config;
use Irwan\Slapper_UI\FormAPI;
use Irwan\Slapper_UI\SimpleForm;
use Irwan\Slapper_UI\CustomForm;
use Irwan\Slapper_UI\Dropdown;
use Irwan\Slapper_UI\Input;
use Irwan\Slapper_UI\CustomFormElement;
use Irwan\Slapper_UI\BaseSelector;
use Irwan\Slapper_UI\Label;

class SlapperUI extends PluginBase implements Listener{
    
    public function onEnable(){
        $this->getLogger()->info("Plugin hasil enable!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->form = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
        switch($cmd->getName()){
            case "slapperui":
                if($sender instanceof Player){
                    $this->openMyForm($sender);
                    return true;
                }else{
                    $sender->sendMessage("Use this cmd in Game!");
                }
            break;
        }
        return true;
    }
    
    public function openMyForm($sender){
       $form = $this->form->createSimpleForm(function(Player $sender, $data){
            $result = $data;
            if($result == null){
                return true;
            }
            switch($result){
                case 0:
                break;
                case 1:
                $this->create($sender);
                break;
                case 2:
                $this->cmd($sender);
                break;
                case 3:
                $this->other($sender);
                break;  
                case 4:
                $command = "slapper remove";
            $this->getServer()->getCommandMap()->dispatch($sender, $command);
                break;
                
            }
        });
        
        $name = $sender->getName();
        
        $form->setTitle("§6» §eSlapperUI §6«§r");
        $form->setContent("§6Hii §f". $name . "\n§6Please select the menu below!");
        $form->addButton("§cEXIT\n§8Tap To Exit", 0, "textures/blocks/barrier");
        $form->addButton("§6CREATE\n§8Tap to crate", 0, "textures/ui/confirm");
        $form->addButton("§6COMMAND\n§8Tap to add/remove commands", 0, "textures/items/banner_pattern");
        $form->addButton("§6OTHER\n§8Tap to see", 0, "textures/ui/magnifyingGlass");
        $form->addButton("§6REMOVE\n§8Tap to remove", 0, "textures/ui/trash");
        $form->sendToPlayer($sender);
        return $form;
    }

    public function create($sender){
        $form = $this->form->createCustomForm(function(Player $sender, $data){
            if($data !== null){
                $dropdownIndex = $data[0];
                $input = $data[1];
                $myArrayName = ["human", "bat", "zombie", "cow", "sheep", "blaze", "spider", "creeper", "chiken", "vex", "wolf", "enderman", "skeleton", "slime", "silverfish", "villager", "guardian", "shulker", "vindicator", "wither"];
                $dropdownValue = $myArrayName[$dropdownIndex];
                $command = "slapper spawn ".$dropdownValue." ".$input;
                $this->getServer()->getCommandMap()->dispatch($sender, $command);
            }
        });
        $form->setTitle("Create Slapper");
        $myArrayName = ["human", "bat", "zombie", "cow", "sheep", "blaze", "spider", "creeper", "chiken", "vex", "wolf", "enderman", "skeleton", "slime", "silverfish", "villager", "guardian", "shulker", "vindicator", "wither"];
        $form->addDropdown("", $myArrayName);
        $form->addInput("name:", "steve");
        $form->sendToPlayer($sender);
    }
    
    public function cmd($sender){
       $form = $this->form->createSimpleForm(function(Player $sender, $data){
            $result = $data;
            if($result == null){
                return true;
            }
            switch($result){
                case 0:
                $command = "sui";
                $this->getServer()->getCommandMap()->dispatch($sender, $command);
                break;
                case 1:
                $this->addcmd($sender);
                break;
                case 2:
                $this->delcmd($sender);
                break;
                
            }
        });
        $form->setTitle("§6» §eSlapperUI §6«§r");
        $form->addButton("§cEXIT\n§8Tap to exit", 0, "textures/ui/cancel");
        $form->addButton("§6ADD COMMAND\n§8Tap to add", 0, "textures/items/banner_pattern");
        $form->addButton("§6DEL COMMAND\n§8Tap to delete", 0, "textures/ui/trash");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function other($sender){
       $form = $this->form->createSimpleForm(function(Player $sender, $data){
            $result = $data;
            if($result == null){
                return true;
            }
            switch($result){
                case 0:
                $command = "sui";
                $this->getServer()->getCommandMap()->dispatch($sender, $command);
                break;
                case 1:
                $this->scale($sender);
                break;
                case 2:
                $command = "slapper id";
                $this->getServer()->getCommandMap()->dispatch($sender, $command);
                break;
                case 3:
                $this->teleports($sender);
                break;
                case 4:
                $this->name($sender);
                break;
                
            }
        });
        $form->setTitle("§6» §eSlapperUI §6«§r");
        $form->addButton("§cEXIT\n§8Tap to exit", 0, "textures/ui/cancel");
        $form->addButton("§6SET SCALE\n§8Tap to set scale", 0, "textures/items/paper");
        $form->addButton("§6CHECK ID\n§8Tap to check id", 0, "textures/ui/magnifyingGlass");
        $form->addButton("§6TELEPORT HERE\n§8Tap to teleport", 0, "textures/ui/icon_import");
        $form->addButton("§6CHANGE NAME\n§8Tap to change name", 0, "textures/items/name_tag");
        $form->sendToPlayer($sender);
        return $form;
    }

    public function addcmd($sender){
        $form = $this->form->createCustomForm(function(Player $sender, $data){
            $result = $data[0];
            if($result === null){
                return true;
            }
            $command = "slapper edit $data[0] addcommand rca {player} $data[1]";
            $this->getServer()->getCommandMap()->dispatch($sender, $command);
        });
        $form->setTitle("§r§6» §eADD COMMANDS §6«§r");
        $form->addInput("Id:", "0");
        $form->addInput("Commands:", "say hi");
        $form->sendToPlayer($sender);
        return $form;
    }

    public function delcmd($sender){
        $form = $this->form->createCustomForm(function(Player $sender, $data){
            $result = $data[0];
            if($result === null){
                return true;
            }
            $command = "slapper edit $data[0] delcommand rca {player} $data[1]";
            $this->getServer()->getCommandMap()->dispatch($sender, $command);
        });
        $form->setTitle("§r§6» §eDEL COMMANDS §6«§r");
        $form->addInput("Id:", "0");
        $form->addInput("Commands:", "say hi");
        $form->sendToPlayer($sender);
    }

    public function scale($sender){
        $form = $this->form->createCustomForm(function(Player $sender, $data){
            $result = $data[0];
            if($result === null){
                return true;
            }
            $command = "slapper edit $data[0] size $data[1]";
            $this->getServer()->getCommandMap()->dispatch($sender, $command);
        });
        $form->setTitle("§r§6» §eSET SCALE §6«§r");
        $form->addInput("Id:", "0");
        $form->addInput("Scale:", "1");
        $form->sendToPlayer($sender);
    }

    public function teleports($sender){
        $form = $this->form->createCustomForm(function(Player $sender, $data){
            $result = $data[0];
            if($result === null){
                return true;
            }
            $command = "slapper edit $data[0] tphere";
            $this->getServer()->getCommandMap()->dispatch($sender, $command);
        });
        $form->setTitle("§r§6» §eTELEPORT HERE §6«§r");
        $form->addInput("Id:", "0");
        $form->sendToPlayer($sender);
    }

    public function name($sender){
        $form = $this->form->createCustomForm(function(Player $sender, $data){
            $result = $data[0];
            if($result === null){
                return true;
            }
            $command = "slapper edit $data[0] name $data[1]";
            $this->getServer()->getCommandMap()->dispatch($sender, $command);
        });
        $form->setTitle("§r§6» §eCHANGE NAME §6«§r");
        $form->addInput("Id:", "0");
        $form->addInput("Name:", "steve");
        $form->sendToPlayer($sender);
    }
}