

model model4

global {
	//INPUT BEGINS:
	
	//change which control policies are on for specific run
	bool masks_on_policy <- false;
	bool build_cap_policy <- false;
	bool soc_dist_policy <- false;
	bool monitoring_symtps_policy <- false;
	bool testing_policy <- false;// true;
	bool contact_tracing_policy <- false;
	bool will_change_conditions <- false;
	
	//change enforcement levels
	float follow_bcp <- 0.8;
	float enf_testing <- 0.2;
	float enf_socdist <- 0.8;
	float perc_wearing_masks <- 0.8; //0.9;
	float enf_monitor <- 0.8;
	
	int change_policy_date <- 3*day;  //after 1 week
	float perc_inperson_classes <- 0.1;
	int upper_testing_limit <- 80;
	
	//change directory path
	//string my_path <- "/Users/nadinemeister/Documents/abm_test/multiple_runs";
	//string my_path <- "/Users/hlan/Documents/abm_test/multiple_runs";
	//string my_path <- "D:/ABM/GMU_results/change_policy/init25000_gradually_change/test1/";
	string my_path <- "D:/ABM/_vaccine/vaccine_only/";
	
	//INPUT ENDS
	
	//COVID-19 specific parameters
	float inf_rate_no_mask <- 0.004366584;//0.004366584;//0.5; /*0.002;*/ //
	float inf_rate_mask <- inf_rate_no_mask*0.15;
	float perc_asympt <- 0.4; //for college students
	float inf_dist <- 2 #m; 
	float death_rate_20s <- 0.001;
	float perc_inf_off_campus <- 0.0001;//0.0001; //percentage of getting infected off campus per person per day
	
	//vaccine parameters
	int init_vaccinated <- 200;
	list<int> new_vaccinated_perday <- [10,10,10,10,10,10,10,10,10,10,
		10,10,10,10,10,10,10,10,10,10,
		10,10,10,10,10,10,10,10,10,10
	];
//	list<int> new_vaccinated_perday <- [0,0,0,0,0,0,0,0,0,0,
//		0,0,0,0,0,0,0,0,0,0,
//		0,0,0,0,0,0,0,0,0,0
//	];
	int vac_ind <- 0;
	float first5 <- 1.0-0.124;
	float second5 <- 1.0-0.347;
	float third5 <- 1.0- 0.345;
	float fourth5 <- 1.0- 0.464;
	float fifth5 <- 1.0- 0.490;
	float sixth5 <- 1.0- 0.756;
	
	//time parameters
	int hour <- 12; //5*12 = 60
	int day <- 13*hour;
	int monitor_checkup_time <- 1*day;
	int testing_checkup_time <- 1*day; //1*day; //3*day;
	
	//spatial and GMU parameters
	//int nb_people <- 10000;
	int nb_infected_init <- 5;
	int numStayingInDorm <- 3350;
	int dead <- 0;
	float step <- 5 #mn;
	file roads_shapefile <- file("../includes/GMU_road_network_730.shp");
	file buildings_shapefile <- file("../includes/GMU_blds_final_v5.shp");
	
	list<building> residential_buildings;
	list<building> non_residential_buildings;
	list<building> dining_buildings;
	
	geometry shape <- envelope(roads_shapefile);	
	graph road_network;
	
	int nb_people_infected <- nb_infected_init update: people count (each.is_infected and each.active);
	int nb_people_inf_show_sympt <- 0 update: people count (each.show_sympt);
	int nb_people_inf_no_sympt <- nb_infected_init update: people count (each.no_sympt and each.active); //nb_people_infected - nb_people_inf_show_sympt; //

//	int nb_people_inf_show_sympt <- int(0.6*nb_infected_init) update: people count (each.show_sympt);
//	int nb_people_inf_no_sympt <- nb_infected_init-nb_people_inf_show_sympt update: people count (each.no_sympt and each.active); //nb_people_infected - nb_people_inf_show_sympt; //

	int nb_people_infected_total <- nb_infected_init update: dead + people count (each.sick_once);
	int nb_people_sick_once <- nb_infected_init update: people count (each.sick_once);
	int nb_active <- nb_people update: people count (each.active);
	int nb_people <- 5000 update: people count(true);
	int nb_people_not_infected <- nb_people - nb_infected_init update: nb_people - nb_people_infected;
	//float infected_rate update: nb_people_infected/nb_people;
	
	int nb_people_vaccinated <- 0 update: people count (each.vaccine_start > 0);
	
	int nb_living_inDorms <- 0 update: people count (each.toDorm);
	int num_close_contacts <- 0 update: people count (each.close_contact);
	
	
	init{
		create road from: roads_shapefile;
		road_network <- as_edge_graph(road);		
		create building from: buildings_shapefile with: [type::string(read ("NATURE")), canDine::int(read ("withDining")), diningCap::int(read ("DiningCap")), maxCap::int(read ("MaxCap"))] {
			if canDine=1{
				color <- #pink;
			} else if type = "Residence" or type="FacultyHousing"{
				color <- #orange;
			} else if type = "Academic" or type="Administrative"{
				color <- #yellow;
			}
		}
		create people {}
		
		create people number:nb_people {
			//initialize location of each person
			targetBuilding <- one_of(building);
			location <- any_location_in(targetBuilding);
			targetBuilding.numHosts <- targetBuilding.numHosts + 1;	
			
			//decide if person is wearing mask
			if masks_on_policy{
				if flip(perc_wearing_masks){
					inf_rate <- inf_rate_mask;
				} 
			}
			
			
			//set incubation period as gaussian distribution, with 5 as mean, and 1 as standard deviation, so 68% of all participants will have an incubation period between 4 and 6
			if flip (0.03){
				incub_pd <- rnd(8,14) * day;
			} else{
				incub_pd <- max([gauss_rnd(5, 1), 2.0]) * day;
			}
			//incub_pd <- 2;
			
			//given age based on age distribution of gmu
			if flip(0.172){
				age <- rnd(18,19);
			} else if flip(0.219){
				age <- rnd(20,21);
			} else if flip (0.223){
				age <- rnd(22,24);
			} else if flip (0.173){
				age <- rnd(25,29);
			} else if flip (0.081){
				age <- rnd(30,34);
			} else {
				age <- rnd (35, 60);
			}
		}
		
 		write("nb_infected_init: " + nb_infected_init);
 		//set initial number of sick people
		ask nb_infected_init among people {
			is_infected <- true;
			sick_start <- 0; //need to fix
//			if flip(0.4){
//			no_sympt <- true;
//			}
//			else{
//			show_sympt <-true;
//			}
			no_sympt <- true;
			sick_once <- true;
//			if flip(nb_people_inf_show_sympt/nb_infected_init) {
//				show_sympt <- true;
//			} else{
//				no_sympt <- true;
//			}
		}
		
		
//		write("nb_people_inf_show_sympt to set:" + nb_people_inf_show_sympt);
		//symptoms
		list<people> inf_people <- people where (each.is_infected = true);
		
		ask nb_people_inf_show_sympt among inf_people{ // //
			//set to show sympt
			show_sympt <- true;
			no_sympt <- false;
		}
		
		write(nb_active);
		//inactive people
		list<people> not_inf_people <- people where (each.is_infected = false);
		
		ask (nb_people-nb_active) among not_inf_people {
				returndate <- cycle + 14*day;
				active <- false;
				location <- {0,0};
				sick_once <- true;
				is_infected <- false;
						
				
				//write ("in testing: " + "cycle: " + string(cycle) + "people: " + name + "no_sympt: " +no_sympt );
						
				if masks_on_policy{
					if flip(perc_wearing_masks){
						inf_rate <- inf_rate_mask;
					} 
				}
						
				if flip (0.03){
					incub_pd <- rnd(8,14) * day;
				} else{
					incub_pd <- max([gauss_rnd(5, 1), 2.0]) * day;
				}
						
				//given age based on age distribution of gmu
				if flip(0.172){
					age <- rnd(18,19);
				} else if flip(0.219){
					age <- rnd(20,21);
				} else if flip (0.223){
					age <- rnd(22,24);
				} else if flip (0.173){
					age <- rnd(25,29);
				} else if flip (0.081){
					age <- rnd(30,34);
				} else {
					age <- rnd (35, 60);
				}
						//radius <- 100;		
			
		}
		
		ask init_vaccinated among (people where (!each.is_infected)) {
			vaccine_start <- 1; 
			inf_rate <- inf_rate * sixth5;
		}
		
		residential_buildings <- building where (each.type="Residence" or each.type="FacultyHousing");
		non_residential_buildings <- building where (each.type!="Residence" and each.type!="FacultyHousing");
		dining_buildings <- building where (each.canDine=1);
		
		//deciding who's staying in dorms
		if (nb_people < numStayingInDorm){
			numStayingInDorm <- nb_people;
		}
		
		ask numStayingInDorm among people{
			toDorm <- true;
			dorm <- one_of(building where ((each.type="Residence" or each.type="FacultyHousing") and (each.livingIn < each.maxCap)));
			dorm.livingIn <- dorm.livingIn + 1;
		}
		
	}

	reflex aff {
		write "Message at cycle " + cycle ;
	}
//	saving to file with specified my_path from input parameters earlier
//	reflex save_initial when: cycle = 0 {
//        save "cycle, nb_people_infected, nb_people_inf_show_sympt, nb_people_inf_no_sympt, nb_people, nb_people_total" to: my_path + "resultHeaders.txt" type: "text" header: false;
//    }
//    
//	reflex save_result {
//        save [cycle, nb_people_infected, nb_people_inf_show_sympt, nb_people_inf_no_sympt, nb_people] to: my_path + cycle +"_results.csv" type: "csv";
//    }
//    
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////change simulation conditions
    
	reflex change_conditions when: cycle = change_policy_date and will_change_conditions{
		
		//your new parameters:
		masks_on_policy <- true;
		build_cap_policy <- true;
		soc_dist_policy <- true;
		monitoring_symtps_policy <- true;
		testing_policy <- true;
		
		follow_bcp <- 0.9;
		enf_testing <- 0.1;
		enf_socdist <- 0.9;
		perc_wearing_masks <- 0.9;
		enf_monitor <- 0.9;
		
		perc_inperson_classes <- 0.1;
	 
		//no one is going to academic buildings anymore
		non_residential_buildings <- building where (each.type!="Residence" and each.type!="FacultyHousing" and each.type != "Academic" and each.type != "Administrative");
	 
		//removing certain number of people to leave (go off campus)
//		int will_stay <- 5000;
//		int numStayingInDorm <- 1100;
//		
//		if nb_people - will_stay > 0{
//			ask (nb_people-will_stay) among people{
//				do die;
//				
//				write ('1st do die');
//			}
//			nb_people <- will_stay;
//			
//				write ('1st nb_people <- will_stay;');
//		}
//		
//		//resetting masks status
//		ask people{
//			inf_rate <- inf_rate_no_mask;
//		}
//		if masks_on_policy{
//			ask (perc_wearing_masks * nb_people) among people{
//				inf_rate <- inf_rate_mask;
//			} 
//		}
//		
//		//resetting number of people in dorms
//		ask people {
//			toDorm <- false;
//		}
//		
//		ask building where (each.type="Residence" or each.type="FacultyHousing") {
//			livingIn <- 0;
//		}
//		
//		ask numStayingInDorm among people{
//			toDorm <- true;
//			dorm <- one_of(building where ((each.type="Residence" or each.type="FacultyHousing") and (each.livingIn < each.maxCap)));
//			dorm.livingIn <- dorm.livingIn + 1;
//		}
//		

///////////////////////////


		int will_stay <- 7000;
		int numStayingInDorm <- 2500;
		
		if nb_people - will_stay > 0{
			ask (nb_people-will_stay) among people{
				do die;
			}
			nb_people <- will_stay;
		}

//		int will_stay <- 25000;
//		int numStayingInDorm <- 2000;
//		
//		if nb_people - will_stay > 0{
//			ask people where (true){
//				if flip((nb_people - will_stay)/nb_people){
//					do die;
//				}
//				
//			}
//			nb_people <- will_stay;
//		}
//		
		
///////////////////////////


		//resetting masks status
		ask people{
			inf_rate <- inf_rate_no_mask;
		}
		if masks_on_policy{
			ask (perc_wearing_masks * nb_people) among people{
				inf_rate <- inf_rate_mask;
			} 
		}
		
		//resetting number of people in dorms
		ask people {
			toDorm <- false;
		}
		
		ask building where (each.type="Residence" or each.type="FacultyHousing") {
			livingIn <- 0;
		}
		
		ask numStayingInDorm among people{
			toDorm <- true;
			dorm <- one_of(building where ((each.type="Residence" or each.type="FacultyHousing") and (each.livingIn < each.maxCap)));
			dorm.livingIn <- dorm.livingIn + 1;
		}
		
				
	}
	
	reflex change_conditions_second when: cycle = (change_policy_date + 2*day) and will_change_conditions{
		
//		//your new parameters:
//		masks_on_policy <- true;
//		build_cap_policy <- true;
//		soc_dist_policy <- true;
//		monitoring_symtps_policy <- true;
//		testing_policy <- true;
//		
//		follow_bcp <- 0.9;
//		enf_testing <- 0.1;
//		enf_socdist <- 0.9;
//		perc_wearing_masks <- 0.9;
//		enf_monitor <- 0.9;
//		
//		perc_inperson_classes <- 0.1;
//	 
//		//no one is going to academic buildings anymore
//		non_residential_buildings <- building where (each.type!="Residence" and each.type!="FacultyHousing" and each.type != "Academic" and each.type != "Administrative");
	 
		//removing certain number of people to leave (go off campus)
		int will_stay <- 5000;// 0.5 * nb_people;
		int numStayingInDorm <- 1800;
		
//		if nb_people - will_stay > 0{
//			ask (nb_people-will_stay) among people{
//				do die;
//				
//				write ('2nd do die');
//			}
//			nb_people <- will_stay;
//			
//				write ('2nd nb_people <- will_stay;');
//		}

		if nb_people - will_stay > 0{
			ask people where (true){
				if flip((nb_people - will_stay)/nb_people){
					do die;
				}
				
			}
			nb_people <- will_stay;
		}
		
		//resetting masks status
		ask people{
			inf_rate <- inf_rate_no_mask;
		}
		if masks_on_policy{
			ask (perc_wearing_masks * nb_people) among people{
				inf_rate <- inf_rate_mask;
			} 
		}
		
		//resetting number of people in dorms
		ask people {
			toDorm <- false;
		}
		
		ask building where (each.type="Residence" or each.type="FacultyHousing") {
			livingIn <- 0;
		}
		
		ask numStayingInDorm among people{
			toDorm <- true;
			dorm <- one_of(building where ((each.type="Residence" or each.type="FacultyHousing") and (each.livingIn < each.maxCap)));
			dorm.livingIn <- dorm.livingIn + 1;
		}
		
		
	}
	
	
	
	reflex change_conditions_third when: cycle = (change_policy_date + 4*day) and will_change_conditions{
		
//		//your new parameters:
//		masks_on_policy <- true;
//		build_cap_policy <- true;
//		soc_dist_policy <- true;
//		monitoring_symtps_policy <- true;
//		testing_policy <- true;
//		
//		follow_bcp <- 0.9;
//		enf_testing <- 0.1;
//		enf_socdist <- 0.9;
//		perc_wearing_masks <- 0.9;
//		enf_monitor <- 0.9;
//		
//		perc_inperson_classes <- 0.1;
//	 
//		//no one is going to academic buildings anymore
//		non_residential_buildings <- building where (each.type!="Residence" and each.type!="FacultyHousing" and each.type != "Academic" and each.type != "Administrative");
	 
		//removing certain number of people to leave (go off campus)
		int will_stay <- 4000;// 0.5 * nb_people;
		int numStayingInDorm <- 1100;
		
//		if nb_people - will_stay > 0{
//			ask (nb_people-will_stay) among people{
//				do die;
//				
//				write ('2nd do die');
//			}
//			nb_people <- will_stay;
//			
//				write ('2nd nb_people <- will_stay;');
//		}

		if nb_people - will_stay > 0{
			ask people where (true){
				if flip((nb_people - will_stay)/nb_people){
					do die;
				}
				
			}
			nb_people <- will_stay;
		}
		
		//resetting masks status
		ask people{
			inf_rate <- inf_rate_no_mask;
		}
		if masks_on_policy{
			ask (perc_wearing_masks * nb_people) among people{
				inf_rate <- inf_rate_mask;
			} 
		}
		
		//resetting number of people in dorms
		ask people {
			toDorm <- false;
		}
		
		ask building where (each.type="Residence" or each.type="FacultyHousing") {
			livingIn <- 0;
		}
		
		ask numStayingInDorm among people{
			toDorm <- true;
			dorm <- one_of(building where ((each.type="Residence" or each.type="FacultyHousing") and (each.livingIn < each.maxCap)));
			dorm.livingIn <- dorm.livingIn + 1;
		}
		
		
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////
	
//	reflex printing {
//		ask people where (each.close_contact){
//			write(name + ": " + is_infected);
//		}
//		write('-----');
//		
//	}

    reflex end_simulation when: cycle = 1560 { //4680 {
		do pause;
    }
    
    //add new vaccinated people every few days
    reflex add_vaccinated when: (cycle mod day = 0) and (cycle != 0){
    	int num_vac_today <- 0;
    	if (vac_ind <length(new_vaccinated_perday)){
    		num_vac_today <- new_vaccinated_perday at vac_ind;
    	}
    	ask num_vac_today among (people where (each.vaccine_start<0)){
    		vaccine_start <- cycle;
    	}
    	vac_ind <- vac_ind + 1;
    }
    
    reflex test_close_contacts when: cycle mod day = 0 and cycle > testing_checkup_time and contact_tracing_policy{
    	write(name + "to be tested earlier");
    	ask people where (each.close_contact){
    		if flip (upper_testing_limit/num_close_contacts){
    			write(name + "to be tested");
	    		if is_infected{
					nb_people <- nb_people -1;
					
					//if going to recover, create another person as inactive and set as active after 2 weeks
					//else, die :(
					if (age >= 18 and age <= 29 and !flip(death_rate_20s)) or (age >29 and age <= 35 and !flip(death_rate_20s*4)) or (age >= 35 and !flip(death_rate_20s*10)){
						create people number:1{
							returndate <- cycle + 14*day;
							active <- false;
							location <- {0,0};
							sick_once <- true;
							is_infected <- false;
														
							if masks_on_policy{
								if flip(perc_wearing_masks){
									inf_rate <- inf_rate_mask;
								} 
							}
							
							if flip (0.03){
								incub_pd <- rnd(8,14) * day;
							} else{
								incub_pd <- max([gauss_rnd(5, 1), 2.0]) * day;
							}
							
							//given age based on age distribution of gmu
							if flip(0.172){
								age <- rnd(18,19);
							} else if flip(0.219){
								age <- rnd(20,21);
							} else if flip (0.223){
								age <- rnd(22,24);
							} else if flip (0.173){
								age <- rnd(25,29);
							} else if flip (0.081){
								age <- rnd(30,34);
							} else {
								age <- rnd (35, 60);
							}
							//radius <- 100;
						}
					} else{
						dead <- dead + 1;
					}
					
					//remove the agent from simulation
					do die;
				}
	    	}
    		}
    		
    }
}

species people skills:[moving]{		
	float speed <- (2 + rnd(3)) #km/#h;//0.01; //
	bool is_infected <- false;
	point target;
	building targetBuilding;
	list<building> buildingType;
	float inf_rate <- inf_rate_no_mask;
	bool show_sympt <- false;
	bool no_sympt <- false;
	int sick_start <- 50000; //arbitrary large number, just must be greater than total cycles
	bool going_to_dine <- false;
	bool toDorm <- false;
	building dorm;
	int returndate <- -5; //arbitrary negative number so it doesn't influence anything
	bool active <- true;
	int incub_pd;// <- 3*day;
	bool asympt <- false;
	bool sick_once <- false;
	int age;
	int radius <- 1;
	bool close_contact <- false;
	int vaccine_start <- -1;
	
//	reflex leave_campus when: (cycle = change_policy_date) and (show_sympt or flip(0.5)) {
//		nb_people <- nb_people -1;
//		do die;
//	}
//	

	reflex nomoreperson0 when: name = 'people0'{
		do die;
	}
	
	reflex returnToCampus when: (monitoring_symtps_policy or testing_policy) and returndate = cycle {
		nb_people <- nb_people +1;
		active <- true;
		
		targetBuilding <- one_of(building);
		location <- any_location_in(targetBuilding);
		targetBuilding.numHosts <- targetBuilding.numHosts + 1;
		
		if masks_on_policy{
			if flip(perc_wearing_masks){
				inf_rate <- inf_rate_mask;
			} 
		}
	}
	
	reflex get_infected_off_campus when: cycle mod day = 0 and !is_infected and active{ //I think people on campus will also go off campus, so could take away the !toDorm condition
		if flip(perc_inf_off_campus){
			is_infected <- true;
			sick_start <- cycle;
			no_sympt <- true;
			if flip (perc_asympt){
				asympt <- true;
				inf_rate <- 0.75 * inf_rate;
			}
			sick_once <- true;
		}
	}
	 
	reflex stay when: target = nil and active {
		//if lunch(12-1pm) or dinner(6-7pm), go to dining hall
		//if evening, some go to residential dorms, some stay in place as if going to go home for the night
		//else morning or afternoon, go to academic buildings

		if (cycle mod day > 4*hour and cycle mod day < 5*hour) or (cycle mod day > 10*hour and cycle mod day < 11*hour) { // by noon, 50% should be out of rooms?
			if flip(0.2){
				if (name = 'people0'){
				write(name);
				write(targetBuilding);
			}
				targetBuilding.numHosts <- targetBuilding.numHosts - 1; //agent is leaving
				targetBuilding <- one_of(dining_buildings);
				buildingType <- dining_buildings;
				going_to_dine <- true;
				target <- any_location_in (targetBuilding);
			}
		} else if (cycle mod day > 12*hour) and toDorm {
			if (name = 'people0'){
				write(name);
				write(targetBuilding);
			}
			targetBuilding.numHosts <- targetBuilding.numHosts - 1; //agent is leaving
			targetBuilding <- dorm;
			target <- any_location_in (targetBuilding);
		} else {
			if flip(0.05) {
				if (name = 'people0'){
				write(name);
				write(targetBuilding);
			}
				targetBuilding.numHosts <- targetBuilding.numHosts - 1; //agent is leaving
				if will_change_conditions and flip(perc_inperson_classes){ //what percentage of classes are still in person
					targetBuilding <- one_of(building where (each.type!="Residence" and each.type!="FacultyHousing"));
					buildingType <- building where (each.type!="Residence" and each.type!="FacultyHousing");
				} else{
					targetBuilding <- one_of(non_residential_buildings);
					buildingType <- non_residential_buildings;
				}
				
				target <- any_location_in (targetBuilding);
			}
		}

	}

	reflex move when: target != nil and active{
		do goto target: target on: road_network;
		
		int specificBuildingCap <- targetBuilding.maxCap;
		
		if (going_to_dine){
			specificBuildingCap <- targetBuilding.diningCap;
		}
		
		if (location = target){
			if build_cap_policy and flip(follow_bcp) and (targetBuilding.numHosts >= specificBuildingCap){
				//go to new one
				targetBuilding <- one_of(buildingType);
				target <- any_location_in (targetBuilding);
				//go to next target in this timestep?
				//do goto target: target on: road_network;
			} else {
				targetBuilding.numHosts <- targetBuilding.numHosts + 1; //agent has arrived 
				target <- nil;
			} 
		}
		
	}
	

	reflex infect when: is_infected and active{
		int numCloseBy <- people at_distance inf_dist count (true);
		float my_inf <- inf_rate;
		if soc_dist_policy and numCloseBy < 3 and flip(enf_socdist){
			ask people at_distance inf_dist {
				if flip(my_inf/2) and !is_infected{
					is_infected <- true;
					sick_start <- cycle;
					no_sympt <- true;
					if flip (perc_asympt){
						asympt <- true;
						inf_rate <- 0.75 * inf_rate;
					}
					sick_once <- true;
				}
				if contact_tracing_policy{
					close_contact <- true;
					radius <- 5;
					
				}
			}
		} else if (build_cap_policy and (target = nil)) {
			ask people at_distance inf_dist {
				if flip(my_inf*0.75) and !is_infected {
					is_infected <- true;
					sick_start <- cycle;
					no_sympt <- true;
					if flip(perc_asympt){
						asympt <- true;
						inf_rate <- 0.75 * inf_rate;
					}
					sick_once <- true;

				}
				if contact_tracing_policy{
					close_contact <- true;
					radius <- 5;
					
				}
			}
		} else {
			ask people at_distance inf_dist {
				if flip(my_inf) and !is_infected{
					is_infected <- true;
					sick_start <- cycle;
					no_sympt <- true;
					if flip(perc_asympt){
						asympt <- true;
						inf_rate <- 0.75 * inf_rate;
					}
					sick_once <- true;

				}
				if contact_tracing_policy{
					close_contact <- true;
					radius <- 5;
					
				}
			}
		}
		//if social distancing applied & < 3 ppl closeby, --> infect at 0.5 lower rate
		//elif building policy applied & inside building, --> infect at 0.75 lower rate
		//else --> infect regularly
		
		//if inside building, inf slightly higher
		//if >building capacity, extra = close contacts? and no soc dist enforcement rate
		
		
		
		//if in building: spread more
			//inf_rate ++
			
			//if too many: put in close contact list, infect even higher rate?
			//for all remaining:
			//if supposed to be soc dist, but not
				//put in contact list, and infect even higher rate
			//else if supposed to be soc dist and are:
				//inf regular higher rate
			//else no soc dist policy:
				//
			
//		if (target = nil){
//			building currentbuilding <- targetBuilding;
//			ask people where (each.targetBuilding = currentbuilding and each.target = nil) {
//				//soc_dist_policy and flip (enf_socdist) and
//				//flip((currentbuilding.maxCap - currentbuilding.numHosts)/currentbuilding.maxCap)
//				if currentbuilding.numHosts > currentbuilding.maxCap{
//					if flip((currentbuilding.numHosts-currentbuilding.maxCap)/currentbuilding.numHosts){
//						if flip(my_inf*1.1) and !is_infected {
//							is_infected <- true;
//							sick_start <- cycle;
//							no_sympt <- true;
//							if flip(perc_asympt){
//								asympt <- true;
//								inf_rate <- 0.75 * inf_rate;
//							}
//							sick_once <- true;
//		
//						}
//						if contact_tracing_policy{
//							close_contact <- true;
//							radius <- 5;
//							
//						}
//					} else{
//						
//					}
//					
//				} else if currentbuilding.numHosts < currentbuilding.maxCap{
//				}
//			}
//				
//				if flip(my_inf*1.1) and !is_infected {
//					is_infected <- true;
//					sick_start <- cycle;
//					no_sympt <- true;
//					if flip(perc_asympt){
//						asympt <- true;
//						inf_rate <- 0.75 * inf_rate;
//					}
//					sick_once <- true;
//
//				}
//				if contact_tracing_policy{
//					close_contact <- true;
//					radius <- 5;
//					
//				}
//			}
//		} else if soc_dist_policy and numCloseBy < 3 and flip(enf_socdist){
//			ask people at_distance inf_dist {
//				if flip(my_inf/2) and !is_infected{
//					is_infected <- true;
//					sick_start <- cycle;
//					no_sympt <- true;
//					if flip (perc_asympt){
//						asympt <- true;
//						inf_rate <- 0.75 * inf_rate;
//					}
//					sick_once <- true;
//				}
//				if contact_tracing_policy{
//					close_contact <- true;
//					radius <- 5;
//				}
//				
//			}
//		} 
//		//else if (build_cap_policy and (target = nil)) {
//		else {
//			ask people at_distance inf_dist {
//				if contact_tracing_policy{
//					close_contact <- true;
//					radius <- 5;
//					
//				}
//				if flip(my_inf) and !is_infected{
//					is_infected <- true;
//					sick_start <- cycle;
//					no_sympt <- true;
//					if flip(perc_asympt){
//						asympt <- true;
//						inf_rate <- 0.75 * inf_rate;
//					}
//					sick_once <- true;
//
//				}
//			}
//		}
//		
		
	}
	
	//update vaccinated people
	reflex update_vaccinated_ppl when: vaccine_start > 0 and (((cycle-vaccine_start) mod 5*day) = 0){
		if ((cycle-vaccine_start) = 5*day){
			inf_rate <- inf_rate / first5 * second5;
		} else if ((cycle-vaccine_start) = 10*day){
			inf_rate <- inf_rate / second5 * third5;
		} else if ((cycle-vaccine_start) = 15*day){
			inf_rate <- inf_rate / third5 * fourth5;
		} else if ((cycle-vaccine_start) = 20*day){
			inf_rate <- inf_rate / fourth5 * fifth5;
		} else {
			inf_rate <- inf_rate / fifth5 * sixth5;
		}
	}
	
	//update infected symptoms/no symptoms every time
	reflex update_symptoms when: (cycle - sick_start) = incub_pd and !asympt and active{
		no_sympt <- false;
		show_sympt <- true;
		
	}
	
	//monitor symptoms every 1,3,7... days (speicfied by monitor_checkup_time)
	reflex monitor_symptoms when: ((cycle mod monitor_checkup_time) = 0) and monitoring_symtps_policy and active{ //and active{
		//if infected and start showing symptoms and following the policy, then send home
		if show_sympt and (nb_people > 0) and flip (enf_monitor){
			nb_people <- nb_people -1;
			
			//if going to recover, create another person as inactive and set as active after 2 weeks
			//else, die :(
			if (age >= 18 and age <= 29 and !flip(death_rate_20s)) or (age >29 and age <= 35 and !flip(death_rate_20s*4)) or (age >= 35 and !flip(death_rate_20s*10)){
				create people number:1{
					returndate <- cycle + 14*day;
					active <- false;
					location <- {0,0};
					sick_once <- true;
					is_infected <- false;
		
					//write ("in monitoring: " + "cycle: " + string(cycle) + "people: " + name + "no_sympt: " +no_sympt );
					//write ("in testing: " + "cycle: " + string(cycle) + "people: " + name + "no_sympt: " +no_sympt );
					
					
					if masks_on_policy{
						if flip(perc_wearing_masks){
							inf_rate <- inf_rate_mask;
						} 
					}
					
					if flip (0.03){
						incub_pd <- rnd(8,14) * day;
					} else{
						incub_pd <- int(max([gauss_rnd(5.0, 1.0), 2.0])) * day;
					}
					
					//given age based on age distribution of gmu
					if flip(0.172){
						age <- rnd(18,19);
					} else if flip(0.219){
						age <- rnd(20,21);
					} else if flip (0.223){
						age <- rnd(22,24);
					} else if flip (0.173){
						age <- rnd(25,29);
					} else if flip (0.081){
						age <- rnd(30,34);
					} else {
						age <- rnd (35, 60);
					}
					
				}
			} else{
				dead <- dead + 1;
			}
			
			do die;
		}
	}
	
	reflex testing when: ((cycle mod testing_checkup_time) = 0)  and cycle != 0  and testing_policy and active{
		//if infected and test comes out positive (after 1 day of waiting for results) and following the policy, then send home	
		if is_infected /*and (cycle - sick_start > day)*/ and (nb_people > 0) and flip (enf_testing){
			nb_people <- nb_people -1;
			
			//if going to recover, create another person as inactive and set as active after 2 weeks
			//else, die :(
			if (age >= 18 and age <= 29 and !flip(death_rate_20s)) or (age >29 and age <= 35 and !flip(death_rate_20s*4)) or (age >= 35 and !flip(death_rate_20s*10)){
				create people number:1{
					returndate <- cycle + 14*day;
					active <- false;
					location <- {0,0};
					sick_once <- true;
					is_infected <- false;
					
			
					//write ("in testing: " + "cycle: " + string(cycle) + "people: " + name + "no_sympt: " +no_sympt );
					
					if masks_on_policy{
						if flip(perc_wearing_masks){
							inf_rate <- inf_rate_mask;
						} 
					}
					
					if flip (0.03){
						incub_pd <- rnd(8,14) * day;
					} else{
						incub_pd <- max([gauss_rnd(5, 1), 2.0]) * day;
					}
					
					//given age based on age distribution of gmu
					if flip(0.172){
						age <- rnd(18,19);
					} else if flip(0.219){
						age <- rnd(20,21);
					} else if flip (0.223){
						age <- rnd(22,24);
					} else if flip (0.173){
						age <- rnd(25,29);
					} else if flip (0.081){
						age <- rnd(30,34);
					} else {
						age <- rnd (35, 60);
					}
					//radius <- 100;
				}
			} else{
				dead <- dead + 1;
			}
			
			//remove the agent from simulation
			do die;
		}
	}
	
	aspect circle {
		 if (!active){
			draw circle(radius) color: #blue;
			
		} else if (is_infected){
			draw circle(5) color: #red;
			
		} else{
			draw circle(radius) color: #green;
		}
	}
}

species road {
	aspect geom {
		draw shape color: #black;
	}
}

species building {
	int numHosts <- 0;
	int maxCap;
	string type;
	int canDine; //0 or 1
	rgb color <- #gray;
	int diningCap;
	int livingIn <- 0;
	
	aspect geom {
		draw shape color: color;
	}
}

experiment covidEXP type: gui {
	parameter "Nb people infected at init" var: nb_infected_init min: 0 max: 10000; //2147;
	
	parameter "masks_on_policy" var: masks_on_policy <- false; //true;
	parameter "build_cap_policy" var: build_cap_policy category: "init"; // <- true;
	parameter "soc_dist_policy" var: soc_dist_policy category: "init"; // <- true;
	parameter "monitoring_symtps_policy" var: monitoring_symtps_policy category: "init"; // <- true;
	parameter "testing_policy" var: testing_policy category: "init"; // <- true;
	parameter "contact_tracing_policy" var: contact_tracing_policy category: "init"; // <- true;
	parameter "will_change_conditions" var: will_change_conditions category: "init"; // <- true;
		
	//change enforcement levels
	parameter "follow_bcp" var: follow_bcp category: "init"; // <- 0.9;
	parameter "enf_testing" var: enf_testing category: "init"; // <- 0.2;
	parameter "enf_socdist" var: enf_socdist category: "init"; // <- 0.9;
	parameter "perc_wearing_masks" var: perc_wearing_masks category: "init"; // <- 0.9;
	parameter "enf_monitor" var: enf_monitor category: "init"; // <- 1.0;
	
	parameter "change_policy_date" var: change_policy_date; // <- 3*day;  //after 1 week
	parameter "perc_inperson_classes" var: perc_inperson_classes; // <- 0.8;
	parameter "upper_testing_limit" var: upper_testing_limit; // <- 80;
	
	//change directory path
	//parameter "my_path" var: my_path; // <- "D:/ABM/GMU_results/change_policy/init25000_gradually_change/test1/";
	//string my_path <- "D:/ABM/UNC_results/_results/init13/test5/";
	
	//INPUT ENDS
	
	//COVID-19 specific parameters
	
	parameter "inf_rate_no_mask" var: inf_rate_no_mask; //<- 0.004366584;//0.5; /*0.002;*/ //
	parameter "inf_rate_mask" var: inf_rate_mask; // <- inf_rate_no_mask*0.15;
	parameter "perc_asympt" var: perc_asympt; // <- 0.4; //for college students
	parameter "inf_dist" var: inf_dist; // <- 2 #m; 
	parameter "death_rate_20s" var: death_rate_20s; // <- 0.001;
	parameter "perc_inf_off_campus" var: perc_inf_off_campus; // <- 0.0001;//0.0001; //percentage of getting infected off campus per person per day
	
	//time parameters
	parameter "hour" var: hour;// <- 12; //5*12 = 60
	parameter "day" var: day; //<- 13*hour;
	parameter "monitor_checkup_time" var: monitor_checkup_time; // <- 1*day;
	parameter "testing_checkup_time" var: testing_checkup_time; // <- 1*day; //3*day;
	parameter "nb_people_infected" var: nb_people_infected; // <- 1*day; //3*day;
	
	//spatial and GMU parameters
	parameter "nb_people" var: nb_people; //<- 5000;
	parameter "nb_infected_init" var: nb_infected_init; // <- 5;
	parameter "numStayingInDorm" var: numStayingInDorm; // <- 5;
	parameter "dead" var: dead; //<- 0;
	parameter "step" var: step; // <- 5 #mn;
	parameter "roads_shapefile" var: roads_shapefile category: "GIS" ; // <- file("../includes/GMU_road_network_730.shp");
	parameter "buildings_shapefile " var: buildings_shapefile category: "GIS" ;//<- file("../includes/GMU_blds_final_v5.shp");
	

	output {
//		monitor "number off campus" value: nb_people-nb_active;//infected_rate;
		monitor "active people" value: nb_active;//infected_rate;
		monitor "nb_people" value: nb_people;//infected_rate;
		monitor "nb_people" value: nb_people;
		monitor "num infected" value: nb_people_infected;
		monitor "num inDorms" value: nb_living_inDorms;
		monitor "masks_policy" value: masks_on_policy;
		monitor "infected show symptoms" value: nb_people_inf_show_sympt;
		monitor "infected no symptoms" value: nb_people_inf_no_sympt;
		monitor "dead" value: dead;
		monitor "sick_once" value: nb_people_sick_once;

		monitor "masks_on_policy" value: masks_on_policy;
		monitor "build_cap_policy" value: build_cap_policy;
		monitor "soc_dist_policy" value: soc_dist_policy;
		monitor "monitoring_symtps_policy" value: monitoring_symtps_policy;
		monitor "testing_policy" value: testing_policy;
		monitor "contact_tracing_policy" value: contact_tracing_policy;
		monitor "total infected people" value: nb_people_infected_total;

		monitor "vaccinated people" value: nb_people_vaccinated;


 		
//			display map {
//			species road aspect:geom;
//			species building aspect:geom;
//			species people aspect:circle;			
//		}
		 
		
		display chart_display refresh: every(1 #cycles) {
	//		chart "Disease spreading" type: series x_tick_unit:30.0 x_label: 'Day: '+((cycle div 30)+1) y_label: 'Number of People'{
	chart "Disease spreading" type: series y_label: 'Number of People'{
//				data "total number of people" value: nb_people color: #purple;
				data "total infected people" value: nb_people_infected_total color: #black;
//				data "active on campus" value: nb_active color: #green;
//				data "sick_once" value: nb_people_sick_once color: #grey;
				data "infected" value: nb_people_infected color: #red;
				data "infected and showing symptoms" value: nb_people_inf_show_sympt color: #pink;
				data "infected but not showing symptoms" value: nb_people_inf_no_sympt color: #orange;
//				data "in dorms" value: nb_living_inDorms color: #blue;
				data "num of vaccinated" value: nb_people_vaccinated color: #blue;
			}
		}
		
	}
}


/////adaptive
//experiment new_simulation type: gui {
//	init{
//		
//	}
//	
//	reflex when: (cycle = 30000) {
//        //create Species B
//        
//        create simulation with:[nb_infected_init::nb_people_infected, 
//        	nb_people_inf_show_sympt::nb_people_inf_show_sympt, 
//        	nb_active::nb_active, 
//        	nb_people::nb_people, 
//        	numStayingInDorm::numStayingInDorm,
//        	masks_on_policy::true,
//			build_cap_policy::true,
//			soc_dist_policy::true,
//			monitoring_symtps_policy::true,
//			testing_policy::false,
//			contact_tracing_policy::false,
//			follow_bcp::0.9,
//			enf_testing::0.2,
//			enf_socdist::0.9,
//			perc_wearing_masks::0.6,
//			enf_monitor::1.0
//        ];
//        
//        create simulation with:[nb_infected_init::nb_people_infected, 
//        	nb_people_inf_show_sympt::nb_people_inf_show_sympt, 
//        	nb_active::nb_active, 
//        	nb_people::nb_people, 
//        	numStayingInDorm::numStayingInDorm,
//        	masks_on_policy::true,
//			build_cap_policy::true,
//			soc_dist_policy::true,
//			monitoring_symtps_policy::true,
//			testing_policy::true,
//			contact_tracing_policy::true,
//			follow_bcp::0.9,
//			enf_testing::0.3,
//			enf_socdist::0.9,
//			perc_wearing_masks::0.8,
//			enf_monitor::1.0
//        ];
//    }
//    
//    output {
////		monitor "number off campus" value: nb_people-nb_active;//infected_rate;
//		monitor "active people" value: nb_active;//infected_rate;
//		monitor "nb_people" value: nb_people;//infected_rate;
////		monitor "nb_people" value: nb_people;
//		monitor "num infected" value: nb_people_infected;
////		monitor "num show symptoms" value: nb_people_inf_show_sympt;
//		monitor "infected show symptoms" value: nb_people_inf_show_sympt;
//		monitor "infected no symptoms" value: nb_people_inf_no_sympt;
//		monitor "masks_policy" value: masks_on_policy;
//		monitor "dead" value: dead;
//		monitor "sick_once" value: nb_people_sick_once;
//		monitor "total infected people" value: nb_people_infected_total;
// 		
////			display map {
////			species road aspect:geom;
////			species building aspect:geom;
////			species people aspect:circle;			
////		}
//		 
//		
//		display chart_display refresh: every(1 #cycles) {
//			chart "Disease spreading" type: series {
////				data "total number of people" value: nb_people color: #purple;
//				data "total infected people" value: nb_people_infected_total color: #black;
////				data "active on campus" value: nb_active color: #green;
////				data "sick_once" value: nb_people_sick_once color: #grey;
//				data "infected" value: nb_people_infected color: #red;
//				data "infected and showing symptoms" value: nb_people_inf_show_sympt color: #pink;
//				data "infected but not showing symptoms" value: nb_people_inf_no_sympt color: #orange;
////				data "in dorms" value: nb_living_inDorms color: #blue;
//			}
//		}
//		
//	}
//
//}
