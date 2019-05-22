<div class="pagination">
    
	<?if( $this->NavPageNomer > 1 ):?>
		<a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.($this->NavPageNomer-1), array('PAGEN_'.$this->NavNum))?>" class="pagination_left"></a>
	<?endif;?>
					
                
					
                    <?if( $this->NavPageNomer-3 > 1 ):?>
                        <a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'=1', array('PAGEN_'.$this->NavNum))?>">1</a>
                        <?if( $this->NavPageNomer-4 != 1 ):?>
                            <a href="javascript:void(0);">...</a>
                        <?endif;?>
                    <?endif;?>

                    <?for($i=1; $i <= $this->NavPageCount; $i++):?>
                        <?if(
                            ($i+3) < $this->NavPageNomer ||
                            ($i-3) > $this->NavPageNomer
                        ) continue;?>
                        
                            <?if( $this->NavPageNomer == $i ):?>
                                <a class="current_page"><?=$i?></a>
                            <?else:?>
                                <a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.$i, array('PAGEN_'.$this->NavNum))?>"><?=$i?></a>
                            <?endif;?>
                        
                    <?endfor;?>

                    <?
                    //NavPageCount
                    //NavPageNomer
                    ?>

                    <?if( $this->NavPageNomer+3 < $this->NavPageCount ):?>
                        <?if( $this->NavPageNomer+4 != $this->NavPageCount ):?>
                            <a href="javascript:void(0);">...</a>
                        <?endif;?>
                        <a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.$this->NavPageCount, array('PAGEN_'.$this->NavNum))?>"><?=$this->NavPageCount?></a>
                    <?endif;?>

				
				
				<?if( $this->NavPageNomer < $this->NavPageCount ):?>
					<a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.($this->NavPageNomer+1), array('PAGEN_'.$this->NavNum))?>" class="pagination_right"></a>					
					
				<?endif;?>
    
</div>