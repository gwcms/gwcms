{call e field=apikey type=pass_visible}
{call e field=model type=text default="gpt-5.4-mini"}
{call e field=food_coefficient type=number step="0.000001" default="0.333333"}
{call e field=housing_coefficient type=number step="0.000001" default="1"}
{call e field=other_coefficient type=number step="0.000001" default="1"}
{call e field=prompt type=textarea height="220px"}
