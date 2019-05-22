<div class="pagination">
    <nav>
	
	<?if( $this->NavPageNomer > 1 ):?>
		<a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.($this->NavPageNomer-1), array('PAGEN_'.$this->NavNum))?>" class="pagination_left"></a>
	<?endif;?>
					
                <ul class="pagination_center">
					
                    <?if( $this->NavPageNomer-3 > 1 ):?>
                        <li><a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'=1', array('PAGEN_'.$this->NavNum))?>">1</a></li>
                        <?if( $this->NavPageNomer-4 != 1 ):?>
                            <li><a href="javascript:void(0);">...</a></li>
                        <?endif;?>
                    <?endif;?>

                    <?for($i=1; $i <= $this->NavPageCount; $i++):?>
                        <?if(
                            ($i+3) < $this->NavPageNomer ||
                            ($i-3) > $this->NavPageNomer
                        ) continue;?>
                        <li <?=($this->NavPageNomer == $i)?'class="active"':''?>>
                            <?if( $this->NavPageNomer == $i ):?>
                                <span><?=$i?></span>
                            <?else:?>
                                <a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.$i, array('PAGEN_'.$this->NavNum))?>"><?=$i?></a>
                            <?endif;?>
                        </li>
                    <?endfor;?>

                    <?
                    //NavPageCount
                    //NavPageNomer
                    ?>

                    <?if( $this->NavPageNomer+3 < $this->NavPageCount ):?>
                        <?if( $this->NavPageNomer+4 != $this->NavPageCount ):?>
                            <li><a href="javascript:void(0);">...</a></li>
                        <?endif;?>
                        <li><a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.$this->NavPageCount, array('PAGEN_'.$this->NavNum))?>"><?=$this->NavPageCount?></a></li>
                    <?endif;?>

                </ul>
				
				
				<?if( $this->NavPageNomer < $this->NavPageCount ):?>
					<a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.($this->NavPageNomer+1), array('PAGEN_'.$this->NavNum))?>" class="pagination_right"></a>					
					
				<?endif;?>
    </nav>
</div>